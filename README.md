# STEPUP — Shoes Catalog & Product Management System

> **Backend Developer Coding Test Project**  
> Built with Laravel 11/12, MySQL, Laravel Sanctum, PHPUnit Automated Testing, and an interactive Vue 3 Frontend Component.

---

## 👟 Overview

**STEPUP** is an elegant shoes catalog and product management platform. The application is designed to demonstrate clean architectural patterns, RESTful API design, relational database modeling, secure authentication & role-based authorization (Admin vs. User), input validation, file uploads, automated testing, and a reactive Vue.js interface following minimalist visual standards.

### 📚 Official Guide Books (Panduan Penggunaan)
Panduan lengkap per peran pengguna telah tersedia di direktori **[`Guide Book/`](Guide%20Book/)**:
* 🛡️ **[Panduan Penggunaan Admin](Guide%20Book/PANDUAN_ADMIN.md)**: Manajemen produk CRUD, upload foto studio, audit logging, phpMyAdmin, dan eksekusi Swagger Admin.
* 🛍️ **[Panduan Penggunaan User Regular & Guest](Guide%20Book/PANDUAN_USER_REGULAR.md)**: Eksplorasi katalog, filter harga & kategori, pemilihan ukuran EU 39–45, belanja tanpa paksaan login, dan sinkronisasi otomatis keranjang belanja ke database MySQL.
* 💻 **[Panduan Teknis & Pengembang](Guide%20Book/PANDUAN_TEKNIS_DEVELOPER.md)**: Arsitektur Docker Compose, automated tests (41 tests), Swagger OpenAPI 3.0, solusi I/O Windows OPcache, dan cheatsheet API.
* 🚀 **[Panduan Deployment Produksi Dokploy & Server SORANTARA](PRODUCTION_DEPLOYMENT.md)**: Deployment ke server Ubuntu HP 260 G3 Mini via Dokploy, Traefik, Cloudflare Tunnel, backup HDD 500GB `/srv/backup`, dan favicon brand.

### 📋 Feature Scope & Acceptance Criteria

| Feature Scope | Status | Notes |
|---|---|---|
| **User Registration (`/api/register`)** | ✅ Done | Email unique validation, password confirmation, role defaults to `user` (admin role cannot be self-assigned). |
| **Authentication & Tokens (`/api/login`, `/api/logout`)** | ✅ Done | Laravel Sanctum personal access tokens with secure revocation upon logout. |
| **User Profile (`/api/user`)** | ✅ Done | Authenticated endpoint displaying active user profile and role. |
| **Role-Based Access Control (RBAC)** | ✅ Done | `admin` and `user` roles. Regular users receive `403 Forbidden` on CUD operations. |
| **Public Shoes Catalog (`/api/products`)** | ✅ Done | Accessible without login. Supports pagination (default 10), sorting, and Eloquent relationships. |
| **Search & Filtering** | ✅ Done | Filter by `search` (name), `category` (slug), `min_price`, and `max_price`. Rejects invalid range with `422`. |
| **Product Detail (`/api/products/{id}`)** | ✅ Done | Returns product with category; returns `404` for non-existent products. |
| **Admin Product Management (CRUD)** | ✅ Done | Admin can create, update, and delete products, including image uploads stored in public disk. |
| **Shopping Cart (`/api/cart`)** | ✅ Done | Regular user cart is persisted in MySQL (`cart_items` table). Guests use local cart with zero forced login, and guest items auto-sync to database upon login. |
| **Interactive Swagger API Docs (`/api/documentation`)** | ✅ Done | Fully interactive OpenAPI 3.0 / Swagger UI with "Try it out" support and Bearer token authorization. |
| **Automated Testing** | ✅ Done | 41 automated tests (199 assertions) across Unit & Feature test suites covering Models, Relationships, Auth, RBAC, Validation, CRUD, Search, Filters, Cart, and Swagger. |
| **Interactive Vue 3 Frontend** | ✅ Done | Contemporary editorial storefront: Vue 3 + TypeScript, Vue Router, Tailwind CSS, Pinia, Vue Sonner, and accessible UI components. |

---

## 🛠️ Technology Stack

- **Backend Framework:** Laravel 11/12 (PHP 8.2+)
- **Database:** MySQL 8.0 (InnoDB, Relational Foreign Keys, Decimal precision) + Docker Compose
- **Database GUI:** phpMyAdmin at `http://localhost:8085`
- **Authentication:** Laravel Sanctum (Bearer Token)
- **Automated Testing:** PHPUnit / Laravel Test Runner (`:memory:` SQLite for testing, MySQL for app)
- **Frontend Framework:** Vue 3 (Composition API `<script setup>`) + TypeScript
- **State Management & Routing:** Pinia + Vue Router (HTML5 History Mode)
- **Styling & Design System:** Tailwind CSS with Geist & Inter contemporary typography
- **UI & Icons:** Radix Vue / shadcn-vue accessible primitives + Lucide Vue Next
- **Notifications & Utilities:** Vue Sonner, VueUse, Axios, clsx, tailwind-merge

---

## 🚀 Quick Start & Installation

### 1. Prerequisites
- **PHP 8.2+** with extensions: `pdo_mysql`, `pdo_sqlite`, `fileinfo`, `mbstring`, `openssl`, `curl`, `gd`
- **Composer 2+**
- **Node.js 18+** & **NPM**
- **MySQL 8+** (or Docker)

### 2. Clone and Setup Environment
```bash
git clone <repository-url>
cd stepup

# Install PHP dependencies
composer install

# Install Frontend dependencies
npm install

# Setup environment configuration
cp .env.example .env
php artisan key:generate
```

### 3. Configure Database (.env)
Edit your `.env` file to point to your MySQL instance:
```ini
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=stepup
DB_USERNAME=stepup
DB_PASSWORD=stepup
```

### 3. Run All Services with Docker (Recommended)

The entire project (Laravel + Vue 3 App, MySQL 8 Database, and phpMyAdmin) is fully containerized. To spin up everything with a single command:

```bash
docker compose up -d --build
```

**Services automatically started and initialized:**
- **STEPUP Web Application & API:** [http://localhost:8000](http://localhost:8000)
  - Pre-built with Vue 3 + TypeScript editorial storefront & admin panel
  - Container automatically runs migrations, storage linking, and seeders on boot
- **phpMyAdmin Web UI:** [http://localhost:8085](http://localhost:8085)
  - Server: `mysql`
  - User: `root` / Password: `root` (or `stepup` / `stepup`)
- **MySQL 8.0 Database:** `127.0.0.1:3306`
  - Database: `stepup`
  - User: `stepup` / Password: `stepup`

---

### 4. Alternative: Running Locally without Docker for App

If you prefer to run only MySQL in Docker and execute Laravel and Vite locally on the host:

```bash
# 1. Start MySQL & phpMyAdmin
docker compose up -d mysql phpmyadmin

# 2. Configure .env with DB_HOST=127.0.0.1
php artisan migrate:fresh --seed
php artisan storage:link

# 3. Build frontend and serve
npm run build
php artisan serve
```
Open your browser at **`http://127.0.0.1:8000`**.

---

## 🔑 Default Accounts (Seeded)

The database seeder automatically provides test credentials:

| Role | Email | Password | Permissions |
|---|---|---|---|
| **Admin** | `admin@stepup.test` | `password123` | Full access (CRUD products, upload images, manage catalog) |
| **Customer User** | `user@stepup.test` | `password123` | Browse catalog, view details, profile. (CUD restricted with `403`) |

> 💡 **Tip:** On the web interface, click **"Sign In / Register"** to access the **Quick Demo Login** section with 1-click buttons for Admin and User.

---

## 🧪 Automated Testing

All acceptance criteria are backed by automated tests located in `tests/Feature`:
- `tests/Feature/AuthTest.php`: Registration, unique emails, password confirmation, role immutability, login, profile, and logout token revocation.
- `tests/Feature/ProductCrudTest.php`: Admin product creation with file upload, updating, deletion (204), user forbidden (403), unauthorized (401), and validation errors (422).
- `tests/Feature/CatalogTest.php`: Public product browsing, product detail, 404 handling, name search, category filter, price range filters, invalid range validation, pagination, and category listing.

To run the test suite:
```bash
php artisan test
```

Expected output:
```text
Pass: 25 tests, 142 assertions (100% passing)
```

---

## 📡 RESTful API Documentation

> 🚀 **Interactive Swagger / OpenAPI UI:**  
> Access the live, interactive Swagger documentation and test all endpoints directly in your browser at:  
> **[http://localhost:8000/api/documentation](http://localhost:8000/api/documentation)**  
> - **OpenAPI 3.0 Raw JSON Specification:** `http://localhost:8000/docs?api-docs.json`  
> - **Authorization in Swagger:** Click the **Authorize 🔓** button in Swagger UI and enter `Bearer <your_token>` generated from `/api/login` to test protected routes.

All API endpoints are prefixed with `/api`. Requests modifying data expect JSON or `multipart/form-data`.

### Authentication Endpoints

#### 1. Register User
- **Method:** `POST /api/register`
- **Access:** Public
- **Request Body:**
  ```json
  {
    "name": "Jane Doe",
    "email": "jane@example.com",
    "password": "password123",
    "password_confirmation": "password123"
  }
  ```
- **Response (201 Created):**
  ```json
  {
    "message": "User registered successfully.",
    "user": {
      "id": 3,
      "name": "Jane Doe",
      "email": "jane@example.com",
      "role": "user"
    },
    "token": "3|plainTextSanctumToken...",
    "token_type": "Bearer"
  }
  ```

#### 2. Login
- **Method:** `POST /api/login`
- **Access:** Public
- **Request Body:**
  ```json
  {
    "email": "admin@stepup.test",
    "password": "password123"
  }
  ```
- **Response (200 OK):**
  ```json
  {
    "message": "Login successful.",
    "user": {
      "id": 1,
      "name": "STEPUP Admin",
      "email": "admin@stepup.test",
      "role": "admin"
    },
    "token": "4|plainTextSanctumToken...",
    "token_type": "Bearer"
  }
  ```

#### 3. Current User Profile
- **Method:** `GET /api/user`
- **Access:** Authenticated (`Authorization: Bearer <token>`)
- **Response (200 OK):**
  ```json
  {
    "user": {
      "id": 1,
      "name": "STEPUP Admin",
      "email": "admin@stepup.test",
      "role": "admin",
      "created_at": "2026-10-21T05:59:33.000000Z"
    }
  }
  ```

#### 4. Logout
- **Method:** `POST /api/logout`
- **Access:** Authenticated (`Authorization: Bearer <token>`)
- **Response (200 OK):**
  ```json
  {
    "message": "Logged out successfully."
  }
  ```

---

### Categories Endpoints

#### 5. List Categories
- **Method:** `GET /api/categories`
- **Access:** Public
- **Response (200 OK):**
  ```json
  {
    "data": [
      { "id": 1, "name": "Running Shoes", "slug": "running-shoes" },
      { "id": 2, "name": "Casual Sneakers", "slug": "casual-sneakers" },
      { "id": 3, "name": "Formal Shoes", "slug": "formal-shoes" }
    ]
  }
  ```

---

### Products Catalog & Management Endpoints

#### 6. List Products (Catalog, Search & Filter)
- **Method:** `GET /api/products`
- **Access:** Public
- **Query Parameters:**
  - `search` *(optional)*: Case-insensitive search on shoe name.
  - `category` *(optional)*: Category slug (e.g. `running-shoes`).
  - `min_price` *(optional)*: Minimum price filter (e.g. `200000`).
  - `max_price` *(optional)*: Maximum price filter (e.g. `600000`).
  - `page` *(optional)*: Page number (default: 1).
  - `per_page` *(optional)*: Items per page (default: 10).
- **Example Request:**
  ```http
  GET /api/products?search=runner&category=running-shoes&min_price=200000&max_price=600000
  ```
- **Response (200 OK):**
  ```json
  {
    "data": [
      {
        "id": 1,
        "name": "STEPUP AeroRun Pro",
        "slug": "stepup-aerorun-pro",
        "description": "Lightweight running shoes for daily training with responsive cushion technology.",
        "price": "450000.00",
        "image": "/storage/products/aerorun-pro.png",
        "category": {
          "id": 1,
          "name": "Running Shoes",
          "slug": "running-shoes"
        },
        "created_at": "2026-10-21T05:59:33.000000Z",
        "updated_at": "2026-10-21T05:59:33.000000Z"
      }
    ],
    "links": {
      "first": "http://127.0.0.1:8000/api/products?page=1",
      "last": "http://127.0.0.1:8000/api/products?page=2",
      "prev": null,
      "next": "http://127.0.0.1:8000/api/products?page=2"
    },
    "meta": {
      "current_page": 1,
      "from": 1,
      "last_page": 2,
      "per_page": 10,
      "total": 12
    }
  }
  ```

#### 7. Get Product Detail
- **Method:** `GET /api/products/{id}`
- **Access:** Public
- **Response (200 OK):**
  ```json
  {
    "data": {
      "id": 1,
      "name": "STEPUP AeroRun Pro",
      "slug": "stepup-aerorun-pro",
      "description": "Lightweight running shoes for daily training...",
      "price": "450000.00",
      "image": "/storage/products/aerorun-pro.png",
      "category": {
        "id": 1,
        "name": "Running Shoes",
        "slug": "running-shoes"
      }
    }
  }
  ```
- **Response (404 Not Found):** If product ID is not found.

#### 8. Create Product (Admin Only)
- **Method:** `POST /api/products`
- **Access:** Admin (`Authorization: Bearer <admin-token>`)
- **Headers:** `Accept: application/json`
- **Content-Type:** `multipart/form-data` or `application/json`
- **Fields:**
  - `name`: string, required, max:255
  - `category_id`: integer, required, exists:categories,id
  - `price`: numeric, required, min:0
  - `description`: string, optional
  - `image`: file (image/jpeg,png,webp) or relative path string, optional
- **Response (201 Created):**
  ```json
  {
    "data": {
      "id": 13,
      "name": "STEPUP Velocity Trainer",
      "slug": "stepup-velocity-trainer",
      "description": "High performance training shoe.",
      "price": "620000.00",
      "image": "/storage/products/random_hash.png",
      "category": {
        "id": 1,
        "name": "Running Shoes",
        "slug": "running-shoes"
      }
    }
  }
  ```

#### 9. Update Product (Admin Only)
- **Method:** `PUT /api/products/{id}` or `POST /api/products/{id}` with `_method: PUT`
- **Access:** Admin (`Authorization: Bearer <admin-token>`)
- **Fields:** `name`, `category_id`, `price`, `description`, `image` (all optional/sometimes).
- **Response (200 OK):** Returns updated `ProductResource`.

#### 10. Delete Product (Admin Only)
- **Method:** `DELETE /api/products/{id}`
- **Access:** Admin (`Authorization: Bearer <admin-token>`)
- **Response (204 No Content):** Empty body on successful deletion.

---

### Cart Endpoints (Regular Users & Database Persistence)

#### 11. Get Cart Items
- **Method:** `GET /api/cart`
- **Access:** Authenticated (`Authorization: Bearer <token>`)
- **Response (200 OK):**
  ```json
  {
    "data": [
      {
        "id": 1,
        "user_id": 2,
        "product_id": 5,
        "size": "42",
        "quantity": 2,
        "product": {
          "id": 5,
          "name": "STEPUP Apex Runner",
          "price": "1450000.00",
          "image": "/storage/products/apex.jpg",
          "category": { "id": 1, "name": "Running Shoes", "slug": "running-shoes" }
        }
      }
    ]
  }
  ```

#### 12. Add Item to Cart
- **Method:** `POST /api/cart`
- **Access:** Authenticated (`Authorization: Bearer <token>`)
- **Body:** `{ "product_id": 5, "size": "42", "quantity": 1 }`
- **Behavior:** If product with the same size already exists in the user's cart, quantity is automatically incremented.
- **Response (201 Created):** Returns the created or updated `CartItemResource`.

#### 13. Update Cart Item Quantity
- **Method:** `PUT /api/cart/{id}`
- **Access:** Authenticated (`Authorization: Bearer <token>`)
- **Body:** `{ "quantity": 4 }`
- **Response (200 OK):** Returns updated `CartItemResource`.

#### 14. Delete Single Cart Item
- **Method:** `DELETE /api/cart/{id}`
- **Access:** Authenticated (`Authorization: Bearer <token>`)
- **Response (204 No Content):** Empty body.

#### 15. Clear All Cart Items
- **Method:** `DELETE /api/cart`
- **Access:** Authenticated (`Authorization: Bearer <token>`)
- **Response (204 No Content):** Empty body.

#### 16. Sync Guest Cart on Login
- **Method:** `POST /api/cart/sync`
- **Access:** Authenticated (`Authorization: Bearer <token>`)
- **Body:**
  ```json
  {
    "items": [
      { "product_id": 1, "size": "42", "quantity": 1 },
      { "product_id": 3, "size": "43", "quantity": 2 }
    ]
  }
  ```
- **Behavior:** Merges items stored locally by guest users into the MySQL database cart upon login without loss of items.
- **Response (200 OK):** Returns updated full cart collection.

---

## 🛡️ Error Handling & Status Codes

| HTTP Status | Condition |
|---|---|
| `200 OK` | Request succeeded. |
| `201 Created` | Resource created successfully. |
| `204 No Content` | Product successfully deleted. |
| `401 Unauthorized` | Missing, expired, or invalid token. |
| `403 Forbidden` | Authenticated user lacks `admin` privileges. |
| `404 Not Found` | Requested product or endpoint does not exist. |
| `422 Unprocessable Content` | Validation failure (e.g. empty name, negative price, min_price > max_price). |
| `500 Server Error` | Unexpected server error. |

Example validation error response (`422`):
```json
{
  "message": "The price must be at least 0. (and 1 other error)",
  "errors": {
    "price": ["The price must be at least 0."],
    "category_id": ["The selected category is invalid."]
  }
}
```

---

## 🎨 Frontend Architecture & Routes (Vue 3 + TypeScript)

The frontend is built as a contemporary fashion editorial and sneaker catalog SPA following the STEPUP brand design system:

| Route Path | View Component | Description | Access |
|---|---|---|---|
| **`/`** | `HomeView.vue` | Editorial hero ("Every step tells a story"), curated featured showcase, category tiles, brand statement, closing banner. | Public |
| **`/products`** | `ProductsView.vue` | "The Collection" catalog with debounced search, category pills, price range presets, sorting, pagination, and URL sync. | Public |
| **`/products/:id`** | `ProductDetailView.vue` | Editorial split layout with large footwear photography, pricing, highlights, specs, and related footwear styles. | Public |
| **`/login`** | `LoginView.vue` | Clean authentication form with password toggle, client validation, and 1-click reviewer demo login buttons. | Guest only |
| **`/register`** | `RegisterView.vue` | Customer registration form strictly enforcing the `user` role. | Guest only |
| **`/admin/products`** | `AdminProductsView.vue` | Protected admin management table, search, category filter, pagination, and accessible deletion modal. | Admin only |
| **`/admin/products/new`** | `ProductFormView.vue` | Publishing form for new styles with live image upload preview and error mapping. | Admin only |
| **`/admin/products/:id/edit`** | `ProductFormView.vue` | Style editor supporting field updates and image replacements via `_method: PUT`. | Admin only |
| **`/*`** | `NotFoundView.vue` | Clean editorial 404 page with home navigation. | Public |

---

## 🙏 Thank You

Terima kasih atas kesempatan dan tantangan yang telah diberikan untuk mengerjakan proyek coding test ini. **STEPUP** dibangun dengan dedikasi penuh mengedepankan *clean architecture*, keamanan API, performa tinggi, *automated testing* menyeluruh, serta pengalaman pengguna yang elegan dan responsif.

Semoga proyek ini dapat memberikan gambaran komprehensif mengenai standar kerja dan kemampuan teknis saya dalam pengembangan aplikasi Full-Stack modern.

*Built with passion, precision, and purpose by **Wasis Wicaksono**.*
