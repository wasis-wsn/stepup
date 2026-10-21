<?php

namespace Tests\Feature;

use Tests\TestCase;

class SwaggerTest extends TestCase
{
    public function test_swagger_ui_page_is_accessible(): void
    {
        $response = $this->get('/api/documentation');

        $response->assertStatus(200);
        $response->assertSee('SwaggerUIBundle');
    }

    public function test_openapi_json_is_accessible_and_valid(): void
    {
        $response = $this->get('/docs');

        $response->assertStatus(200);
        $response->assertJsonPath('openapi', '3.0.0');
        $response->assertJsonPath('info.title', 'STEPUP — Shoes Catalog & Product Management RESTful API');
        $response->assertJsonStructure([
            'openapi',
            'info',
            'paths' => [
                '/register',
                '/login',
                '/user',
                '/logout',
                '/categories',
                '/products',
                '/products/{id}',
                '/cart',
                '/cart/{id}',
                '/cart/sync',
            ],
        ]);
    }
}
