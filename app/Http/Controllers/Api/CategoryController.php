<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use OpenApi\Attributes as OA;

class CategoryController extends Controller
{
    /**
     * Display a listing of categories.
     */
    #[OA\Get(
        path: '/categories',
        summary: 'List all categories',
        description: 'Retrieve all available shoe categories sorted alphabetically.',
        tags: ['Categories'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'List of product categories',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'data',
                            type: 'array',
                            items: new OA\Items(
                                properties: [
                                    new OA\Property(property: 'id', type: 'integer', example: 1),
                                    new OA\Property(property: 'name', type: 'string', example: 'Running Shoes'),
                                    new OA\Property(property: 'slug', type: 'string', example: 'running-shoes'),
                                    new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
                                    new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
                                ]
                            )
                        )
                    ]
                )
            )
        ]
    )]
    public function index(): AnonymousResourceCollection
    {
        $categories = Category::orderBy('name')->get();

        return CategoryResource::collection($categories);
    }
}
