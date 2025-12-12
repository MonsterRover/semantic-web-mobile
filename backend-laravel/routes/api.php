<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\SearchController;
use App\Http\Controllers\API\SkripsiController;
use App\Http\Controllers\API\OntologyController;
use App\Http\Controllers\API\UserController;

// Public routes
Route::get('/', function () {
    return response()->json([
        'message' => 'Semantic Web API - Thesis Title Search System',
        'version' => '1.0.0',
        'status' => 'active',
        'endpoints' => [
            'auth' => [
                'POST /api/login' => 'Login',
                'POST /api/logout' => 'Logout',
                'GET /api/me' => 'Get current user',
                'GET /api/auth/check' => 'Check authentication',
            ],
            'search' => [
                'GET /api/search?q=keyword&topik=&tahun=' => 'Semantic search',
                'GET /api/search/suggestions?q=partial' => 'Search suggestions',
            ],
            'skripsi' => [
                'GET /api/skripsi/{id}' => 'Get skripsi detail',
                'GET /api/skripsi/{id}/download' => 'Download skripsi file',
            ],
        ],
    ]);
});

// Authentication routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth');
Route::get('/me', [AuthController::class, 'me'])->middleware('auth');
Route::get('/auth/check', [AuthController::class, 'check']);

// Public search routes (accessible to everyone including non-authenticated users)
Route::get('/search', [SearchController::class, 'search']);
Route::get('/search/suggestions', [SearchController::class, 'suggestions']);

// Public skripsi detail routes
Route::get('/skripsi/{id}', [SkripsiController::class, 'show']);
Route::get('/skripsi/{id}/download', [SkripsiController::class, 'download']);

// Protected routes - Kaprodi & Admin
Route::middleware(['auth', 'role:kaprodi,admin'])->group(function () {
    // Skripsi upload and management
    Route::post('/skripsi', [SkripsiController::class, 'store']);
    Route::put('/skripsi/{id}', [SkripsiController::class, 'update']);
    Route::delete('/skripsi/{id}', [SkripsiController::class, 'destroy']);
    Route::get('/skripsi/my/uploads', [SkripsiController::class, 'myUploads']);
});

// Protected routes - Admin only
Route::middleware(['auth', 'role:admin'])->group(function () {
    // User management
    Route::get('/users', [UserController::class, 'index']);
    Route::post('/users', [UserController::class, 'store']);
    Route::put('/users/{id}', [UserController::class, 'update']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']);

    // Ontology management
    Route::get('/ontology', [OntologyController::class, 'index']);
    Route::post('/ontology/upload', [OntologyController::class, 'upload']);
    Route::post('/ontology/{id}/activate', [OntologyController::class, 'setActive']);
    Route::delete('/ontology/{id}', [OntologyController::class, 'destroy']);

    // All skripsi (admin view)
    Route::get('/admin/skripsi', [SkripsiController::class, 'index']);
});

// Public ontology info
Route::get('/ontology/current', [OntologyController::class, 'current']);

