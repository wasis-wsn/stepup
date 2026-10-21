<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_can_be_created_with_valid_attributes(): void
    {
        $category = Category::create([
            'name' => 'Running Shoes',
            'slug' => 'running-shoes',
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'STEPUP UltraBoost',
            'slug' => 'stepup-ultraboost',
            'description' => 'High performance cushioned runner',
            'price' => 1250000.00,
            'image' => 'products/sample.jpg',
        ]);

        $this->assertDatabaseHas('products', [
            'name' => 'STEPUP UltraBoost',
            'slug' => 'stepup-ultraboost',
        ]);

        $this->assertEquals('1250000.00', $product->price);
        $this->assertEquals($category->id, $product->category_id);
    }

    public function test_product_belongs_to_category_relationship(): void
    {
        $category = Category::create([
            'name' => 'Casual Sneakers',
            'slug' => 'casual-sneakers',
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'STEPUP Classic Low',
            'slug' => 'stepup-classic-low',
            'price' => 450000.00,
        ]);

        $this->assertInstanceOf(Category::class, $product->category);
        $this->assertEquals('Casual Sneakers', $product->category->name);
    }

    public function test_category_has_many_products_relationship(): void
    {
        $category = Category::create([
            'name' => 'Formal Shoes',
            'slug' => 'formal-shoes',
        ]);

        Product::create([
            'category_id' => $category->id,
            'name' => 'Oxford Monarch',
            'slug' => 'oxford-monarch',
            'price' => 850000.00,
        ]);

        Product::create([
            'category_id' => $category->id,
            'name' => 'Derby Executive',
            'slug' => 'derby-executive',
            'price' => 920000.00,
        ]);

        $this->assertCount(2, $category->products);
    }

    public function test_user_is_admin_check(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $regularUser = User::factory()->create(['role' => 'user']);

        $this->assertTrue($admin->isAdmin());
        $this->assertFalse($regularUser->isAdmin());
    }
}
