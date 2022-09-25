# API laravel para crawler de iso (moeda)

requisitos: **PHP** 7.4, **laravel Framework** 8.83.23, **banco de dados**: mysql

# Instalação
composer install
precisa criar uma base nomeado como "captura"
php artisan migrate
alterar DB_DATABASE para o banco captura

# Endpoints
Para consultar use o caminho /api/captura
Ex: http://localhost:8000/api/captura/?code=brl
ou faça um requisição POST com os seguintes parametros

```json
{  
  "code": "GBP"
}
```
OU 
```json
{  
  "code_list": ["GBP", "GEL", "HKD"]  
}
```
OU 
```json
{  
  "number": [242]  
}
```
OU
```json
{
  "number_lists": [242, 324]  
}
```
```json
[
    {
        "code": "brl",
        "number": "986",
        "decimal": "2",
        "currency": "Real",
        "location": {
            "1": {
                "location": "Brasil",
                "icon": "upload.wikimedia.org/wikipedia/commons/thumb/0/05/Flag_of_Brazil.svg/22px-Flag_of_Brazil.svg.png"
            }
        }
    }
]
```

Acima o exemplo de retorno para o parametro ["code" => "BRL"]

# TESTE UNITÁRIO
php artisan test


