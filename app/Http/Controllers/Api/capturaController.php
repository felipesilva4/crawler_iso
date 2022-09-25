<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\captura;
use GuzzleHttp;;

class capturaController extends Controller
{
    public function crawler(Request $request)
    {   
        $apiParams = [
            $code = $request->input('code'),
            $code_list = $request->input('code_list'),
            $number = $request->input('number'),
            $number_list = $request->input('number_list'),
        ];

        // Se nenhum parametro válido for passado, retorna erro
        if ($apiParams[0] == null && $apiParams[1] == null && $apiParams[2] == null && $apiParams[3] == null) {
            return response()->json([
                'message' => 'Parâmetros inválidos',
            ], 400);
        }
        
        try {
            $results = $this->getCaptura($apiParams);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Não existe resultado para esse Código ou número',
            ], 500);
        }
        
        $this->saveResults($results);
        return $results;
    }

    function getCaptura($apiParams)
    {
        $client = new GuzzleHttp\Client();
        $res = $client->request('GET', 'https://pt.wikipedia.org/wiki/ISO_4217');
        $res = str_replace(array("\r", "\n"), '', $res->getBody());
        
        if ($apiParams[0] != NULL || $apiParams[1] != Null) {
            return $results = $this->searchByCode($res, $apiParams);
        } else {
            return $results = $this->searchByNumber($res, $apiParams);
        }
    }

    function searchByNumber($res, $apiParams)
    {
        $codes = [];
        if ($apiParams[2] != NULL || $apiParams[3] != Null) {
            $apiParams[2] != NULL ? $code = $apiParams[2] : $code = $apiParams[3];
            $code = explode(',', $code); // Transforma em array
            $code = array_map('trim', $code); // Remove espaços em branco
            foreach ($code as $keyCode => $value) {
                $getCodePartPattern = '/.....\/...<td>'.$value.'<\/td>/i';
                preg_match_all($getCodePartPattern, $res, $codePart);
                $getCodePattern = '/>(.*?)<\/td/i';
                preg_match_all($getCodePattern, $codePart[0][0], $codeFinal);
                $codes[$keyCode] = $codeFinal[1][0];
            }
        }
        // return $codes;
        return $this->searchByCode($res, $apiParams=[$codes]);
        // return $codePart;
    }

    function searchByCode($res, $apiParams)
    {
        $results = [];
        if ($apiParams[0] != NULL || $apiParams[1] != Null) {
            $apiParams[0] != NULL ? $code = $apiParams[0] : $code = $apiParams[1];
            if (!is_array($code)) {
                $code = explode(',', $code);
            } 
            foreach ($code as $keyCode => $value) {
                // limpas as variaveis  locationsAndIcons e responseCrawler,pois concatenam os resultados de cada iteração
                $locationAndIcons = [];
                $responseCrawler = [];
                $value = trim($value);
                $dataPattern = '/<td>'.$value.'(.*?)<\/tr><tr>/i';
                $numberPattern = '/<\/td><td>(.*?)</i';
                // $nomePattern = '/<td>(.*?)<\/td><td>/i';
                preg_match_all($dataPattern, $res, $match);
                preg_match_all($numberPattern, $match[1][0], $number);

                $currencyPattern = '/'.$number[1][0].'<\/td><td>(.*?)</i';
                preg_match_all($currencyPattern,$match[1][0], $decimal);
                $decimalPattern = '/\">(.*?)<\/a>/i';
                $locationsPattern = '/title.*?">(.*?)<\/a>/i';
                $iconsPattern = '/src=\"\/\/(.*?)\"/i';
                preg_match_all($iconsPattern, $match[1][0], $icons);
                preg_match_all($decimalPattern, $match[1][0], $currency);
                preg_match_all($locationsPattern, $match[1][0], $location);
 
                foreach ($location[1] as $key => $value) {
                    // primeiro icone será diferente do local, esse if corrige isso
                    try {
                        if ($value != $location[1][0]) {
                            if (!isset($icons[1][$key])) {
                                $icons[1][$key] = '';
                            }
                            $locationAndIcons[$key] = [
                                'location' => $location[1][$key],
                                'icon' => $icons[1][$key-1], // -1 para pegar o icone correto
                            ];
                        }
                    } catch (\Throwable $th) {
                        $locationAndIcons = [
                            'location' => '',
                            'icon' => '',
                        ];
                    }
                }
                if (!isset($locationAndIcons)) {
                    $locationAndIcons = [
                        'location' => '',
                        'icon' => '',
                    ];
                }
                $responseCrawler = [
                    'code' => $code[$keyCode],
                    'number' => $number[1][0],
                    'decimal' => $decimal[1][0],
                    'currency' => $currency[1][0],
                    'location' => $locationAndIcons,
                ];

                $results[$keyCode] = $responseCrawler;
            }

        }

        return $results;
    }

    function saveResults($results)
    {
        $location = [];
        $icons = [];
        // O salvamento é ordenado, se houver 5 paises mas só tiver 3 icones, os 2 ultimos paises não terão icones
        foreach ($results as $key => $value) {
            // $value['location'] = json_encode($value['location']);
            foreach ($value['location'] as $keyLocation => $valueLocation) {
                $location[$keyLocation] = $valueLocation['location'];
                $icons[$keyLocation] = $valueLocation['icon'];
            }
        }
        // return [$location, $icons];
        foreach ($results as $key => $value) {
            foreach ($value['location'] as $alterkeyLocation => $alterValueLocation) {
                $captura = new captura();
                $captura->code = $value['code'];
                $captura->number = $value['number'];
                $captura->decimal = $value['decimal'];
                $captura->currency = $value['currency'];
                $captura->location = json_encode($alterValueLocation);
                $captura->icon = json_encode($icons[$alterkeyLocation]);
                $captura->save();
            }
        }
    }
}