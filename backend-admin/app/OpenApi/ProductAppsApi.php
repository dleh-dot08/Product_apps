<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    title: 'Product Apps API',
    description: 'API router untuk aplikasi mobile Product Apps. Semua endpoint memerlukan router API key. Endpoint selain login juga memerlukan token Laravel Sanctum.'
)]
#[OA\Server(url: '/api', description: 'Product Apps API')]
#[OA\SecurityScheme(
    securityScheme: 'RouterApiKey',
    type: 'http',
    scheme: 'bearer',
    bearerFormat: 'Base64 API key',
    description: 'Gunakan API key router hardcoded sebagai Bearer token.'
)]
#[OA\SecurityScheme(
    securityScheme: 'SanctumToken',
    type: 'apiKey',
    in: 'header',
    name: 'X-Auth-Token',
    description: 'Token Sanctum dari response login. Tidak perlu awalan Bearer.'
)]
#[OA\Tag(name: 'Authentication')]
#[OA\Tag(name: 'Users')]
#[OA\Tag(name: 'Pickup Tasks')]
#[OA\Tag(name: 'Drivers')]
#[OA\Tag(name: 'Packaging')]
final class ProductAppsApi
{
    #[OA\Post(
        path: '/login',
        tags: ['Authentication'],
        summary: 'Login pengguna',
        security: [['RouterApiKey' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['email', 'password', 'device_name'],
                properties: [
                    new OA\Property(property: 'email', type: 'string', format: 'email'),
                    new OA\Property(property: 'password', type: 'string', format: 'password'),
                    new OA\Property(property: 'device_name', type: 'string', example: 'android'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Login berhasil'),
            new OA\Response(response: 401, description: 'API key tidak valid'),
            new OA\Response(response: 422, description: 'Kredensial atau input tidak valid'),
        ]
    )]
    public function login(): void {}

    #[OA\Get(path: '/user', tags: ['Authentication'], summary: 'Data pengguna aktif', security: [['RouterApiKey' => [], 'SanctumToken' => []]], responses: [new OA\Response(response: 200, description: 'Data pengguna'), new OA\Response(response: 401, description: 'Tidak terautentikasi')])]
    public function user(): void {}

    #[OA\Post(path: '/logout', tags: ['Authentication'], summary: 'Logout pengguna', security: [['RouterApiKey' => [], 'SanctumToken' => []]], responses: [new OA\Response(response: 200, description: 'Logout berhasil'), new OA\Response(response: 401, description: 'Tidak terautentikasi')])]
    public function logout(): void {}

    #[OA\Get(path: '/users', tags: ['Users'], summary: 'Daftar pengguna (API key saja)', security: [['RouterApiKey' => []]], responses: [new OA\Response(response: 200, description: 'Daftar pengguna')])]
    public function usersIndex(): void {}

    #[OA\Post(path: '/users', tags: ['Users'], summary: 'Buat pengguna', security: [['RouterApiKey' => [], 'SanctumToken' => []]], requestBody: new OA\RequestBody(content: new OA\JsonContent(type: 'object')), responses: [new OA\Response(response: 201, description: 'Pengguna dibuat'), new OA\Response(response: 422, description: 'Input tidak valid')])]
    public function usersStore(): void {}

    #[OA\Get(path: '/users/{id}', tags: ['Users'], summary: 'Detail pengguna (API key saja)', security: [['RouterApiKey' => []]], parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))], responses: [new OA\Response(response: 200, description: 'Detail pengguna'), new OA\Response(response: 404, description: 'Tidak ditemukan')])]
    public function usersShow(): void {}

    #[OA\Put(path: '/users/{id}', tags: ['Users'], summary: 'Update pengguna', security: [['RouterApiKey' => [], 'SanctumToken' => []]], parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))], requestBody: new OA\RequestBody(content: new OA\JsonContent(type: 'object')), responses: [new OA\Response(response: 200, description: 'Pengguna diperbarui')])]
    public function usersUpdate(): void {}

    #[OA\Delete(path: '/users/{id}', tags: ['Users'], summary: 'Hapus pengguna', security: [['RouterApiKey' => [], 'SanctumToken' => []]], parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))], responses: [new OA\Response(response: 200, description: 'Pengguna dihapus')])]
    public function usersDestroy(): void {}

    #[OA\Get(path: '/driver/dashboard', tags: ['Drivers'], summary: 'Ringkasan dashboard driver', security: [['RouterApiKey' => [], 'SanctumToken' => []]], responses: [new OA\Response(response: 200, description: 'Ringkasan dashboard')])]
    public function driverDashboard(): void {}

    #[OA\Get(path: '/pickup', tags: ['Pickup Tasks'], summary: 'Daftar tugas pickup/delivery', security: [['RouterApiKey' => [], 'SanctumToken' => []]], parameters: [new OA\Parameter(name: 'status', in: 'query', schema: new OA\Schema(type: 'string')), new OA\Parameter(name: 'search', in: 'query', schema: new OA\Schema(type: 'string')), new OA\Parameter(name: 'date', in: 'query', schema: new OA\Schema(type: 'string', format: 'date'))], responses: [new OA\Response(response: 200, description: 'Daftar tugas')])]
    public function pickupIndex(): void {}

    #[OA\Post(path: '/pickup', tags: ['Pickup Tasks'], summary: 'Buat tugas pickup', security: [['RouterApiKey' => [], 'SanctumToken' => []]], requestBody: new OA\RequestBody(content: new OA\JsonContent(type: 'object')), responses: [new OA\Response(response: 201, description: 'Tugas dibuat'), new OA\Response(response: 422, description: 'Input tidak valid')])]
    public function pickupStore(): void {}

    #[OA\Get(path: '/pickup/{id}', tags: ['Pickup Tasks'], summary: 'Detail tugas', security: [['RouterApiKey' => [], 'SanctumToken' => []]], parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string', format: 'uuid'))], responses: [new OA\Response(response: 200, description: 'Detail tugas'), new OA\Response(response: 404, description: 'Tidak ditemukan')])]
    public function pickupShow(): void {}

    #[OA\Patch(path: '/pickup/{id}/status', tags: ['Pickup Tasks'], summary: 'Update status tugas', security: [['RouterApiKey' => [], 'SanctumToken' => []]], parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string', format: 'uuid'))], requestBody: new OA\RequestBody(content: new OA\JsonContent(type: 'object')), responses: [new OA\Response(response: 200, description: 'Status diperbarui'), new OA\Response(response: 422, description: 'Input tidak valid')])]
    public function pickupStatus(): void {}

    #[OA\Post(path: '/pickup/{id}/expenses', tags: ['Pickup Tasks'], summary: 'Tambah pengeluaran tugas', security: [['RouterApiKey' => [], 'SanctumToken' => []]], parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string', format: 'uuid'))], requestBody: new OA\RequestBody(content: new OA\JsonContent(type: 'object')), responses: [new OA\Response(response: 201, description: 'Pengeluaran dibuat')])]
    public function pickupExpense(): void {}

    #[OA\Post(path: '/driver/location', tags: ['Drivers'], summary: 'Update lokasi driver', security: [['RouterApiKey' => [], 'SanctumToken' => []]], requestBody: new OA\RequestBody(content: new OA\JsonContent(type: 'object')), responses: [new OA\Response(response: 200, description: 'Lokasi diperbarui')])]
    public function driverLocationUpdate(): void {}

    #[OA\Get(path: '/driver/locations', tags: ['Drivers'], summary: 'Lokasi driver aktif (API key saja)', security: [['RouterApiKey' => []]], responses: [new OA\Response(response: 200, description: 'Daftar lokasi')])]
    public function driverLocations(): void {}

    #[OA\Get(path: '/packaging/search-so', tags: ['Packaging'], summary: 'Cari sales order', security: [['RouterApiKey' => []]], parameters: [new OA\Parameter(name: 'q', in: 'query', schema: new OA\Schema(type: 'string')), new OA\Parameter(name: 'date_from', in: 'query', schema: new OA\Schema(type: 'string', format: 'date')), new OA\Parameter(name: 'date_to', in: 'query', schema: new OA\Schema(type: 'string', format: 'date'))], responses: [new OA\Response(response: 200, description: 'Hasil pencarian'), new OA\Response(response: 502, description: 'Upstream gagal')])]
    public function packagingSearchSo(): void {}
}
