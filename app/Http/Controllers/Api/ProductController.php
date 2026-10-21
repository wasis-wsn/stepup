<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use OpenApi\Attributes as OA;

class ProductController extends Controller
{
    /**
     * Display a listing of products with search, filter, and pagination.
     */
    #[OA\Get(
        path: '/products',
        summary: 'List and filter footwear catalog',
        description: 'Browse the shoes catalog with optional full-text search, category slug filter, price range bounding, and pagination.',
        tags: ['Products'],
        parameters: [
            new OA\Parameter(name: 'search', in: 'query', description: 'Search term matching product name', required: false, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'category', in: 'query', description: 'Filter by category slug (e.g., running-shoes, casual-sneakers)', required: false, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'min_price', in: 'query', description: 'Minimum price filter', required: false, schema: new OA\Schema(type: 'number', minimum: 0)),
            new OA\Parameter(name: 'max_price', in: 'query', description: 'Maximum price filter (must be >= min_price)', required: false, schema: new OA\Schema(type: 'number', minimum: 0)),
            new OA\Parameter(name: 'per_page', in: 'query', description: 'Items per page (default: 10, max: 100)', required: false, schema: new OA\Schema(type: 'integer', default: 10)),
            new OA\Parameter(name: 'page', in: 'query', description: 'Page number', required: false, schema: new OA\Schema(type: 'integer', default: 1)),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Paginated list of footwear products',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'data',
                            type: 'array',
                            items: new OA\Items(
                                properties: [
                                    new OA\Property(property: 'id', type: 'integer', example: 1),
                                    new OA\Property(property: 'name', type: 'string', example: 'STEPUP Aero Runner Pro'),
                                    new OA\Property(property: 'slug', type: 'string', example: 'stepup-aero-runner-pro'),
                                    new OA\Property(property: 'description', type: 'string', nullable: true, example: 'Lightweight performance road runner.'),
                                    new OA\Property(property: 'price', type: 'string', example: '1250000.00'),
                                    new OA\Property(property: 'image', type: 'string', nullable: true, example: '/storage/products/runner.jpg'),
                                    new OA\Property(
                                        property: 'category',
                                        properties: [
                                            new OA\Property(property: 'id', type: 'integer', example: 1),
                                            new OA\Property(property: 'name', type: 'string', example: 'Running Shoes'),
                                            new OA\Property(property: 'slug', type: 'string', example: 'running-shoes'),
                                        ]
                                    ),
                                ]
                            )
                        ),
                        new OA\Property(
                            property: 'meta',
                            properties: [
                                new OA\Property(property: 'current_page', type: 'integer', example: 1),
                                new OA\Property(property: 'last_page', type: 'integer', example: 2),
                                new OA\Property(property: 'total', type: 'integer', example: 12),
                            ]
                        )
                    ]
                )
            ),
            new OA\Response(response: 422, description: 'Validation error (e.g. min_price > max_price)')
        ]
    )]
    public function index(Request $request): AnonymousResourceCollection
    {
        $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:120'],
            'min_price' => ['nullable', 'numeric', 'min:0'],
            'max_price' => [
                'nullable',
                'numeric',
                'min:0',
                $request->filled('min_price') ? 'gte:min_price' : '',
            ],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ], [
            'max_price.gte' => 'The max price must be greater than or equal to min price.',
        ]);

        $query = Product::with('category');

        // Search by product name
        if ($request->filled('search')) {
            $search = $request->query('search');
            $query->where('name', 'like', "%{$search}%");
        }

        // Filter by category slug
        if ($request->filled('category')) {
            $categorySlug = $request->query('category');
            $query->whereHas('category', function ($q) use ($categorySlug) {
                $q->where('slug', $categorySlug);
            });
        }

        // Filter by minimum price
        if ($request->filled('min_price')) {
            $query->where('price', '>=', (float) $request->query('min_price'));
        }

        // Filter by maximum price
        if ($request->filled('max_price')) {
            $query->where('price', '<=', (float) $request->query('max_price'));
        }

        $perPage = (int) $request->query('per_page', 10);
        $products = $query->latest('id')->paginate($perPage);

        return ProductResource::collection($products);
    }

    /**
     * Store a newly created product in storage (Admin only).
     */
    #[OA\Post(
        path: '/products',
        summary: 'Create a new product (Admin only)',
        description: 'Publish a new shoe style to the catalog with optional photography upload.',
        tags: ['Products'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'multipart/form-data',
                schema: new OA\Schema(
                    required: ['name', 'category_id', 'price'],
                    properties: [
                        new OA\Property(property: 'name', type: 'string', example: 'STEPUP Trail Strider'),
                        new OA\Property(property: 'category_id', type: 'integer', example: 1),
                        new OA\Property(property: 'price', type: 'number', format: 'float', example: 580000),
                        new OA\Property(property: 'discount', type: 'integer', example: 10, description: 'Discount percentage (0-100)'),
                        new OA\Property(property: 'color', type: 'string', example: 'Olive Green / Charcoal'),
                        new OA\Property(property: 'sizes', type: 'string', example: '39, 40, 41, 42, 43, 44, 45'),
                        new OA\Property(property: 'description', type: 'string', nullable: true, example: 'Design & Construction details.'),
                        new OA\Property(property: 'detail', type: 'string', nullable: true, example: 'Highlights and key feature points.'),
                        new OA\Property(property: 'image', type: 'string', format: 'binary', nullable: true),
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Product successfully created'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 403, description: 'Forbidden - Admin privilege required'),
            new OA\Response(response: 422, description: 'Validation error')
        ]
    )]
    public function store(StoreProductRequest $request): JsonResponse
    {
        $data = $request->validated();

        $data['slug'] = $this->generateUniqueSlug($data['name']);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product = Product::create($data);
        $product->load('category');

        Log::info("Product created: [ID: {$product->id}] '{$product->name}' by Admin [User ID: {$request->user()->id}]");

        return (new ProductResource($product))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified product.
     */
    #[OA\Get(
        path: '/products/{id}',
        summary: 'Get product details by ID',
        description: 'Retrieve detailed information for a specific product including category association.',
        tags: ['Products'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', description: 'Product ID', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Product details retrieved successfully'),
            new OA\Response(response: 404, description: 'Product not found')
        ]
    )]
    public function show(int|string $id): ProductResource
    {
        $product = Product::with('category')->findOrFail($id);

        return new ProductResource($product);
    }

    /**
     * Update the specified product in storage (Admin only).
     */
    #[OA\Put(
        path: '/products/{id}',
        summary: 'Update an existing product (Admin only)',
        description: 'Update product name, price, category, description, or image.',
        tags: ['Products'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', description: 'Product ID', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'multipart/form-data',
                schema: new OA\Schema(
                    properties: [
                        new OA\Property(property: '_method', type: 'string', example: 'PUT', description: 'Method spoofing for multipart uploads'),
                        new OA\Property(property: 'name', type: 'string', example: 'STEPUP Trail Strider V2'),
                        new OA\Property(property: 'category_id', type: 'integer', example: 1),
                        new OA\Property(property: 'price', type: 'number', example: 620000),
                        new OA\Property(property: 'discount', type: 'integer', example: 15, description: 'Discount percentage (0-100)'),
                        new OA\Property(property: 'color', type: 'string', example: 'Olive Green / Charcoal'),
                        new OA\Property(property: 'sizes', type: 'string', example: '39, 40, 41, 42, 43, 44, 45'),
                        new OA\Property(property: 'description', type: 'string', nullable: true, example: 'Design & Construction details.'),
                        new OA\Property(property: 'detail', type: 'string', nullable: true, example: 'Highlights and key feature points.'),
                        new OA\Property(property: 'image', type: 'string', format: 'binary', nullable: true),
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Product updated successfully'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 403, description: 'Forbidden - Admin privilege required'),
            new OA\Response(response: 404, description: 'Product not found'),
            new OA\Response(response: 422, description: 'Validation error')
        ]
    )]
    public function update(UpdateProductRequest $request, int|string $id): ProductResource
    {
        $product = Product::findOrFail($id);
        $data = $request->validated();

        if (isset($data['name']) && $data['name'] !== $product->name) {
            $data['slug'] = $this->generateUniqueSlug($data['name'], $product->id);
        }

        if ($request->hasFile('image')) {
            // Delete old file if stored locally in storage
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);
        $product->load('category');

        Log::info("Product updated: [ID: {$product->id}] '{$product->name}' by Admin [User ID: {$request->user()->id}]");

        return new ProductResource($product);
    }

    /**
     * Remove the specified product from storage (Admin only).
     */
    #[OA\Delete(
        path: '/products/{id}',
        summary: 'Delete a product (Admin only)',
        description: 'Permanently remove a shoe product and its associated stored photography.',
        tags: ['Products'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', description: 'Product ID to delete', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 204, description: 'Product deleted successfully'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 403, description: 'Forbidden - Admin privilege required'),
            new OA\Response(response: 404, description: 'Product not found')
        ]
    )]
    public function destroy(Request $request, int|string $id): Response
    {
        $product = Product::findOrFail($id);

        if ($product->image && Storage::disk('public')->exists($product->image)) {
            Storage::disk('public')->delete($product->image);
        }

        $productName = $product->name;
        $productId = $product->id;
        $product->delete();

        Log::info("Product deleted: [ID: {$productId}] '{$productName}' by Admin [User ID: " . ($request->user()?->id ?? 'unknown') . "]");

        return response()->noContent();
    }

    /**
     * Helper to generate unique slug.
     */
    protected function generateUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $slug = Str::slug($name);
        $original = $slug;
        $counter = 1;

        while (
            Product::where('slug', $slug)
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = "{$original}-{$counter}";
            $counter++;
        }

        return $slug;
    }
}
