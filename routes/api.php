<?php

use App\Http\Controllers\Api\V1\ProjectController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// RenovaHub's own API. External providers are not called from these routes.
Route::prefix('v1')->middleware(['auth:sanctum', 'throttle:api'])->group(function () {
    Route::get('projects', [ProjectController::class, 'index']);
    Route::post('projects', [ProjectController::class, 'store']);
    Route::get('projects/{project}', [ProjectController::class, 'show']);
    Route::put('projects/{project}', [ProjectController::class, 'update']);
    Route::delete('projects/{project}', [ProjectController::class, 'destroy']);
    Route::get('projects/{project}/tasks', [ProjectController::class, 'tasks']);
    Route::get('projects/{project}/documents', [ProjectController::class, 'documents']);
    Route::get('projects/{project}/quotations', [ProjectController::class, 'quotations']);
    Route::get('projects/{project}/change-requests', [ProjectController::class, 'changeRequests']);
    Route::post('projects/{project}/change-requests', [ProjectController::class, 'storeChangeRequest']);
    Route::get('projects/{project}/messages', [ProjectController::class, 'messages']);
    Route::post('projects/{project}/messages', [ProjectController::class, 'storeMessage']);
    Route::get('projects/{project}/payments', [ProjectController::class, 'payments']);
    Route::get('projects/{project}/notifications', [ProjectController::class, 'notifications']);
    Route::post('projects/{project}/quotations/{quotation}/approve', [ProjectController::class, 'approveQuotation']);
    Route::post('projects/{project}/quotations/{quotation}/reject', [ProjectController::class, 'rejectQuotation']);
});
