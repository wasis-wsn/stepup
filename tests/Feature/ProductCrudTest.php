<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductCrudTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $regularUser;
    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
        $this->regularUser = User::factory()->create(['role' => 'user']);
        $this->category = Category::factory()->create([
            'name' => 'Running Shoes',
            'slug' => 'running-shoes',
        ]);
    }

    public function test_admin_can_create_product_with_image(): void
    {
        Storage::fake('public');

        $image = UploadedFile::fake()->image('shoe.jpg', 600, 600);

        $response = $this->actingAs($this->admin, 'sanctum')->postJson('/api/products', [
            'name' => 'STEPUP Velocity 1',
            'category_id' => $this->category->id,
            'description' => 'Top tier running shoe.',
            'price' => 599000.00,
            'image' => $image,
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.name', 'STEPUP Velocity 1')
            ->assertJsonPath('data.slug', 'stepup-velocity-1')
            ->assertJsonPath('data.price', '599000.00')
            ->assertJsonPath('data.category.id', $this->category->id);

        $product = Product::first();
        $this->assertNotNull($product);
        $this->assertEquals('STEPUP Velocity 1', $product->name);
        Storage::disk('public')->assertExists($product->image);
    }

    public function test_admin_can_update_product_and_image(): void
    {
        Storage::fake('public');

        $product = Product::factory()->create([
            'category_id' => $this->category->id,
            'name' => 'Original Name',
            'price' => 200000.00,
        ]);

        $newImage = UploadedFile::fake()->image('new-shoe.png', 400, 400);

        $response = $this->actingAs($this->admin, 'sanctum')->putJson("/api/products/{$product->id}", [
            'name' => 'Updated Name',
            'price' => 250000.00,
            'image' => $newImage,
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.name', 'Updated Name')
            ->assertJsonPath('data.price', '250000.00');

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Updated Name',
            'slug' => 'updated-name',
        ]);

        $updatedProduct = $product->fresh();
        Storage::disk('public')->assertExists($updatedProduct->image);
    }

    public function test_admin_can_delete_product(): void
    {
        Storage::fake('public');

        $product = Product::factory()->create([
            'category_id' => $this->category->id,
        ]);

        $response = $this->actingAs($this->admin, 'sanctum')->deleteJson("/api/products/{$product->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('products', [
            'id' => $product->id,
        ]);
    }

    public function test_regular_user_cannot_create_product(): void
    {
        $response = $this->actingAs($this->regularUser, 'sanctum')->postJson('/api/products', [
            'name' => 'Unauthorized Shoe',
            'category_id' => $this->category->id,
            'price' => 300000,
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseEmpty('products');
    }

    public function test_regular_user_cannot_update_product(): void
    {
        $product = Product::factory()->create([
            'category_id' => $this->category->id,
            'name' => 'Original',
        ]);

        $response = $this->actingAs($this->regularUser, 'sanctum')->putJson("/api/products/{$product->id}", [
            'name' => 'Hacked Name',
        ]);

        $response->assertStatus(403);
        $this->assertEquals('Original', $product->fresh()->name);
    }

    public function test_regular_user_cannot_delete_product(): void
    {
        $product = Product::factory()->create([
            'category_id' => $this->category->id,
        ]);

        $response = $this->actingAs($this->regularUser, 'sanctum')->deleteJson("/api/products/{$product->id}");

        $response->assertStatus(403);
        $this->assertDatabaseHas('products', ['id' => $product->id]);
    }

    public function test_unauthenticated_user_cannot_access_product_cud_endpoints(): void
    {
        $product = Product::factory()->create(['category_id' => $this->category->id]);

        $this->postJson('/api/products', ['name' => 'Test'])->assertStatus(401);
        $this->putJson("/api/products/{$product->id}", ['name' => 'Test'])->assertStatus(401);
        $this->deleteJson("/api/products/{$product->id}")->assertStatus(401);
    }

    public function test_product_creation_validation_errors(): void
    {
        // 1. Empty name
        $resName = $this->actingAs($this->admin, 'sanctum')->postJson('/api/products', [
            'name' => '',
            'category_id' => $this->category->id,
            'price' => 100000,
        ]);
        $resName->assertStatus(422)->assertJsonValidationErrors(['name']);

        // 2. Negative price
        $resPrice = $this->actingAs($this->admin, 'sanctum')->postJson('/api/products', [
            'name' => 'Negative Shoe',
            'category_id' => $this->category->id,
            'price' => -50000,
        ]);
        $resPrice->assertStatus(422)->assertJsonValidationErrors(['price']);

        // 3. Non-existent category
        $resCat = $this->actingAs($this->admin, 'sanctum')->postJson('/api/products', [
            'name' => 'No Cat Shoe',
            'category_id' => 999999,
            'price' => 150000,
        ]);
        $resCat->assertStatus(422)->assertJsonValidationErrors(['category_id']);
    }
}
