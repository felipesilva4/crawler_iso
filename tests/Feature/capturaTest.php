<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class capturaTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_crawler()
    {
        // testa o http status code
        $response = $this->get('/api/captura/?code1=&code_list=&number=&code=USD');
        $response->assertStatus(200);
        
    }
}
