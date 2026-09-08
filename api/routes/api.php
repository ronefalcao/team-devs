<?php

use App\Http\Controllers\Api\PrdController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\SearchController;
use App\Http\Controllers\Api\SpecController;
use Illuminate\Support\Facades\Route;

Route::middleware('mcp.token')->group(function () {
    Route::get('projects', [ProjectController::class, 'index']);
    Route::get('projects/{project:slug}/context', [ProjectController::class, 'context']);

    Route::get('search', [SearchController::class, 'index']);

    Route::post('prds', [PrdController::class, 'store']);
    Route::post('prds/{prd}/approve', [PrdController::class, 'approve']);

    Route::post('specs', [SpecController::class, 'store']);
    Route::post('specs/{spec}/approve', [SpecController::class, 'approve']);
});
