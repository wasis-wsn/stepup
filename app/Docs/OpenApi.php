<?php

namespace App\Docs;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    title: 'STEPUP — Shoes Catalog & Product Management RESTful API',
    description: 'Comprehensive RESTful API documentation for STEPUP shoes catalog, user authentication (Sanctum), role-based access control (Admin vs User), and database-persisted shopping cart.',
    contact: new OA\Contact(
        name: 'STEPUP Engineering',
        email: 'support@stepup.test'
    )
)]
#[OA\Server(
    url: 'http://localhost:8000/api',
    description: 'Local Docker / Development Server'
)]
#[OA\SecurityScheme(
    securityScheme: 'bearerAuth',
    type: 'http',
    scheme: 'bearer',
    bearerFormat: 'Sanctum',
    description: 'Provide Sanctum Bearer token in the Authorization header (e.g., Bearer {token})'
)]
#[OA\Tag(name: 'Authentication', description: 'User registration, login, current profile, and logout')]
#[OA\Tag(name: 'Categories', description: 'Product category taxonomy')]
#[OA\Tag(name: 'Products', description: 'Footwear catalog and admin product management')]
#[OA\Tag(name: 'Cart', description: 'User shopping cart management with database persistence')]
class OpenApi
{
}
