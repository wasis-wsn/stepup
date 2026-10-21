<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogTest extends TestCase
{
    use RefreshDatabase;

    private Category $runningCategory;
    private Category $casualCategory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->runningCategory = Category::factory()->create([
            'name' => 'Running Shoes',
            'slug' => 'running-shoes',
        ]);

        $this->casualCategory = Category::factory()->create([
            'name' => 'Casual Sneakers',
            'slug' => 'casual-sneakers',
        ]);
    }

    public function test_public_user_can_view_product_list(): void
    {
        Product::factory()->count(3)->create([
            'category_id' => $this->runningCategory->id,
        ]);

        $response = $this->getJson('/api/products');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'slug',
                        'description',
                        'price',
                        'image',
                        'category' => ['id', 'name', 'slug'],
                    ],
                ],
                'links',
                'meta',
            ]);
    }

    public function test_public_user_can_view_product_detail(): void
    {
        $product = Product::factory()->create([
            'category_id' => $this->runningCategory->id,
            'name' => 'STEPUP AeroRun',
            'price' => 450000.00,
        ]);

        $response = $this->getJson("/api/products/{$product->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $product->id)
            ->assertJsonPath('data.name', 'STEPUP AeroRun')
            ->assertJsonPath('data.category.slug', 'running-shoes');
    }

    public function test_viewing_non_existent_product_returns_404(): void
    {
        $response = $this->getJson('/api/products/999999');

        $response->assertStatus(404);
    }

    public function test_can_filter_products_by_search_name(): void
    {
        Product::factory()->create([
            'category_id' => $this->runningCategory->id,
            'name' => 'STEPUP Air Swift',
        ]);

        Product::factory()->create([
            'category_id' => $this->runningCategory->id,
            'name' => 'STEPUP Classic Low',
        ]);

        $response = $this->getJson('/api/products?search=swift');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'STEPUP Air Swift');
    }

    public function test_can_filter_products_by_category_slug(): void
    {
        Product::factory()->create([
            'category_id' => $this->runningCategory->id,
            'name' => 'Running Shoe 1',
        ]);

        Product::factory()->create([
            'category_id' => $this->casualCategory->id,
            'name' => 'Casual Shoe 1',
        ]);

        $response = $this->getJson('/api/products?category=running-shoes');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.category.slug', 'running-shoes');
    }

    public function test_can_filter_products_by_price_range(): void
    {
        Product::factory()->create([
            'category_id' => $this->runningCategory->id,
            'name' => 'Budget Shoe',
            'price' => 150000.00,
        ]);

        Product::factory()->create([
            'category_id' => $this->runningCategory->id,
            'name' => 'Mid Shoe',
            'price' => 450000.00,
        ]);

        Product::factory()->create([
            'category_id' => $this->runningCategory->id,
            'name' => 'Expensive Shoe',
            'price' => 900000.00,
        ]);

        $response = $this->getJson('/api/products?min_price=200000&max_price=500000');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Mid Shoe');
    }

    public function test_invalid_price_range_returns_422_validation_error(): void
    {
        $response = $this->getJson('/api/products?min_price=600000&max_price=200000');

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['max_price']);
    }

    public function test_products_are_paginated_with_default_10_per_page(): void
    {
        Product::factory()->count(15)->create([
            'category_id' => $this->runningCategory->id,
        ]);

        $response = $this->getJson('/api/products');

        $response->assertStatus(200)
            ->assertJsonCount(10, 'data')
            ->assertJsonPath('meta.per_page', 10)
            ->assertJsonPath('meta.total', 15);
    }

    public function test_public_user_can_view_categories_list(): void
    {
        $response = $this->getJson('/api/categories');

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name', 'slug'],
                ],
            ]);
    }
}
