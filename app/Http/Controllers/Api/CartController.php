<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CartItemResource;
use App\Models\CartItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use OpenApi\Attributes as OA;

class CartController extends Controller
{
    /**
     * Display all cart items for the authenticated user.
     */
    #[OA\Get(
        path: '/cart',
        summary: 'Get user shopping cart',
        description: 'Retrieve all cart items persisted in the MySQL database for the authenticated regular user.',
        tags: ['Cart'],
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: 'User shopping cart items',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'data',
                            type: 'array',
                            items: new OA\Items(
                                properties: [
                                    new OA\Property(property: 'id', type: 'integer', example: 1),
                                    new OA\Property(property: 'user_id', type: 'integer', example: 2),
                                    new OA\Property(property: 'product_id', type: 'integer', example: 5),
                                    new OA\Property(property: 'size', type: 'string', example: '42'),
                                    new OA\Property(property: 'quantity', type: 'integer', example: 2),
                                    new OA\Property(
                                        property: 'product',
                                        properties: [
                                            new OA\Property(property: 'id', type: 'integer', example: 5),
                                            new OA\Property(property: 'name', type: 'string', example: 'STEPUP Apex Runner'),
                                            new OA\Property(property: 'price', type: 'string', example: '1450000.00'),
                                            new OA\Property(property: 'image', type: 'string', example: '/storage/products/apex.jpg'),
                                        ]
                                    ),
                                ]
                            )
                        )
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthenticated')
        ]
    )]
    public function index(Request $request): AnonymousResourceCollection
    {
        $items = $request->user()
            ->cartItems()
            ->with('product.category')
            ->latest('id')
            ->get();

        return CartItemResource::collection($items);
    }

    /**
     * Add a product to the user's cart in the database.
     */
    #[OA\Post(
        path: '/cart',
        summary: 'Add item to shopping cart',
        description: 'Add a product and selected size to user database cart. If product & size exists, quantity is incremented.',
        tags: ['Cart'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['product_id'],
                properties: [
                    new OA\Property(property: 'product_id', type: 'integer', example: 1),
                    new OA\Property(property: 'size', type: 'string', example: '42'),
                    new OA\Property(property: 'quantity', type: 'integer', minimum: 1, maximum: 99, example: 1),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Item added or incremented in database cart'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 422, description: 'Validation error')
        ]
    )]
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'size' => ['nullable', 'max:10'],
            'quantity' => ['nullable', 'integer', 'min:1', 'max:99'],
        ]);

        $userId = $request->user()->id;
        $productId = (int) $validated['product_id'];
        $size = (string) ($validated['size'] ?? '42');
        $quantity = (int) ($validated['quantity'] ?? 1);

        $cartItem = CartItem::where('user_id', $userId)
            ->where('product_id', $productId)
            ->where('size', $size)
            ->first();

        if ($cartItem) {
            $cartItem->increment('quantity', $quantity);
            $cartItem->refresh();
        } else {
            $cartItem = CartItem::create([
                'user_id' => $userId,
                'product_id' => $productId,
                'size' => $size,
                'quantity' => $quantity,
            ]);
        }

        $cartItem->load('product.category');

        return (new CartItemResource($cartItem))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Update quantity of a specific cart item.
     */
    #[OA\Put(
        path: '/cart/{id}',
        summary: 'Update cart item quantity',
        description: 'Update the quantity of a specific item in the database cart.',
        tags: ['Cart'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', description: 'Cart Item ID', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['quantity'],
                properties: [
                    new OA\Property(property: 'quantity', type: 'integer', minimum: 1, maximum: 99, example: 3)
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Quantity updated successfully'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 404, description: 'Cart item not found')
        ]
    )]
    public function update(Request $request, int|string $id): JsonResponse
    {
        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1', 'max:99'],
        ]);

        $cartItem = $request->user()
            ->cartItems()
            ->findOrFail($id);

        $cartItem->update([
            'quantity' => (int) $validated['quantity'],
        ]);

        $cartItem->load('product.category');

        return (new CartItemResource($cartItem))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * Remove an item from the cart.
     */
    #[OA\Delete(
        path: '/cart/{id}',
        summary: 'Remove an item from cart',
        description: 'Delete a specific item from the database cart.',
        tags: ['Cart'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', description: 'Cart Item ID to delete', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 204, description: 'Item removed successfully'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 404, description: 'Cart item not found')
        ]
    )]
    public function destroy(Request $request, int|string $id): Response
    {
        $cartItem = $request->user()
            ->cartItems()
            ->findOrFail($id);

        $cartItem->delete();

        return response()->noContent();
    }

    /**
     * Clear all items from the user's cart.
     */
    #[OA\Delete(
        path: '/cart',
        summary: 'Clear entire cart',
        description: 'Empty all cart items from the database for the authenticated user.',
        tags: ['Cart'],
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(response: 204, description: 'Cart cleared successfully'),
            new OA\Response(response: 401, description: 'Unauthenticated')
        ]
    )]
    public function clear(Request $request): Response
    {
        $request->user()->cartItems()->delete();

        return response()->noContent();
    }

    /**
     * Sync guest localStorage cart items into the user's database cart upon login.
     */
    #[OA\Post(
        path: '/cart/sync',
        summary: 'Sync guest cart to database',
        description: 'Merge local guest shopping cart items into MySQL database cart upon login.',
        tags: ['Cart'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['items'],
                properties: [
                    new OA\Property(
                        property: 'items',
                        type: 'array',
                        items: new OA\Items(
                            required: ['product_id'],
                            properties: [
                                new OA\Property(property: 'product_id', type: 'integer', example: 1),
                                new OA\Property(property: 'size', type: 'string', example: '42'),
                                new OA\Property(property: 'quantity', type: 'integer', example: 1),
                            ]
                        )
                    )
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Cart successfully merged and synchronized'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 422, description: 'Validation error')
        ]
    )]
    public function sync(Request $request): AnonymousResourceCollection
    {
        $validated = $request->validate([
            'items' => ['required', 'array'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.size' => ['nullable', 'max:10'],
            'items.*.quantity' => ['nullable', 'integer', 'min:1', 'max:99'],
        ]);

        $userId = $request->user()->id;

        foreach ($validated['items'] as $item) {
            $productId = (int) $item['product_id'];
            $size = (string) ($item['size'] ?? '42');
            $quantity = (int) ($item['quantity'] ?? 1);

            $cartItem = CartItem::where('user_id', $userId)
                ->where('product_id', $productId)
                ->where('size', $size)
                ->first();

            if ($cartItem) {
                $cartItem->increment('quantity', $quantity);
            } else {
                CartItem::create([
                    'user_id' => $userId,
                    'product_id' => $productId,
                    'size' => $size,
                    'quantity' => $quantity,
                ]);
            }
        }

        $allItems = $request->user()
            ->cartItems()
            ->with('product.category')
            ->latest('id')
            ->get();

        return CartItemResource::collection($allItems);
    }
}
