<?php

namespace Tests\Feature;

use App\Models\CartItem;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private User $otherUser;
    private Product $product;
    private Product $product2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'role' => 'user',
        ]);

        $this->otherUser = User::factory()->create([
            'role' => 'user',
        ]);

        $category = Category::create([
            'name' => 'Running Shoes',
            'slug' => 'running-shoes',
        ]);

        $this->product = Product::create([
            'category_id' => $category->id,
            'name' => 'STEPUP Test Runner',
            'slug' => 'stepup-test-runner',
            'description' => 'Test runner shoe',
            'price' => 500000.00,
            'image' => 'products/aerorun-white.jpg',
        ]);

        $this->product2 = Product::create([
            'category_id' => $category->id,
            'name' => 'STEPUP Test Casual',
            'slug' => 'stepup-test-casual',
            'description' => 'Test casual shoe',
            'price' => 350000.00,
            'image' => 'products/urban-black-gum.jpg',
        ]);
    }

    public function test_unauthenticated_user_cannot_access_cart(): void
    {
        $response = $this->getJson('/api/cart');
        $response->assertStatus(401);

        $response = $this->postJson('/api/cart', [
            'product_id' => $this->product->id,
            'size' => '42',
            'quantity' => 1,
        ]);
        $response->assertStatus(401);
    }

    public function test_regular_user_can_view_cart_items(): void
    {
        CartItem::create([
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
            'size' => '42',
            'quantity' => 2,
        ]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/cart');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.product_id', $this->product->id)
            ->assertJsonPath('data.0.quantity', 2)
            ->assertJsonPath('data.0.size', '42');
    }

    public function test_regular_user_can_add_product_to_cart_in_database(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson('/api/cart', [
                'product_id' => $this->product->id,
                'size' => '43',
                'quantity' => 1,
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.product_id', $this->product->id)
            ->assertJsonPath('data.size', '43')
            ->assertJsonPath('data.quantity', 1);

        $this->assertDatabaseHas('cart_items', [
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
            'size' => '43',
            'quantity' => 1,
        ]);
    }

    public function test_adding_same_product_and_size_increments_quantity(): void
    {
        CartItem::create([
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
            'size' => '42',
            'quantity' => 1,
        ]);

        $response = $this->actingAs($this->user)
            ->postJson('/api/cart', [
                'product_id' => $this->product->id,
                'size' => '42',
                'quantity' => 2,
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.quantity', 3);

        $this->assertDatabaseHas('cart_items', [
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
            'size' => '42',
            'quantity' => 3,
        ]);
    }

    public function test_regular_user_can_update_cart_item_quantity(): void
    {
        $item = CartItem::create([
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
            'size' => '42',
            'quantity' => 1,
        ]);

        $response = $this->actingAs($this->user)
            ->putJson("/api/cart/{$item->id}", [
                'quantity' => 4,
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.quantity', 4);

        $this->assertDatabaseHas('cart_items', [
            'id' => $item->id,
            'quantity' => 4,
        ]);
    }

    public function test_regular_user_can_delete_cart_item(): void
    {
        $item = CartItem::create([
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
            'size' => '42',
            'quantity' => 1,
        ]);

        $response = $this->actingAs($this->user)
            ->deleteJson("/api/cart/{$item->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('cart_items', [
            'id' => $item->id,
        ]);
    }

    public function test_regular_user_can_clear_cart(): void
    {
        CartItem::create([
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
            'size' => '42',
            'quantity' => 1,
        ]);

        CartItem::create([
            'user_id' => $this->user->id,
            'product_id' => $this->product2->id,
            'size' => '44',
            'quantity' => 2,
        ]);

        $response = $this->actingAs($this->user)
            ->deleteJson('/api/cart');

        $response->assertStatus(204);

        $this->assertDatabaseCount('cart_items', 0);
    }

    public function test_user_cannot_modify_another_users_cart_item(): void
    {
        $item = CartItem::create([
            'user_id' => $this->otherUser->id,
            'product_id' => $this->product->id,
            'size' => '42',
            'quantity' => 1,
        ]);

        $response = $this->actingAs($this->user)
            ->putJson("/api/cart/{$item->id}", [
                'quantity' => 5,
            ]);

        $response->assertStatus(404);

        $response = $this->actingAs($this->user)
            ->deleteJson("/api/cart/{$item->id}");

        $response->assertStatus(404);
    }

    public function test_cart_add_validation_fails_for_non_existent_product(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson('/api/cart', [
                'product_id' => 99999,
                'size' => '42',
                'quantity' => 1,
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['product_id']);
    }

    public function test_regular_user_can_sync_guest_items_to_database(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson('/api/cart/sync', [
                'items' => [
                    [
                        'product_id' => $this->product->id,
                        'size' => '41',
                        'quantity' => 2,
                    ],
                    [
                        'product_id' => $this->product2->id,
                        'size' => '43',
                        'quantity' => 1,
                    ],
                ],
            ]);

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');

        $this->assertDatabaseHas('cart_items', [
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
            'size' => '41',
            'quantity' => 2,
        ]);

        $this->assertDatabaseHas('cart_items', [
            'user_id' => $this->user->id,
            'product_id' => $this->product2->id,
            'size' => '43',
            'quantity' => 1,
        ]);
    }
}
