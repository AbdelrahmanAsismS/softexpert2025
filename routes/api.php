<?php
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

Route::middleware(['auth:sanctum', 'manager'])->group(function () {
    Route::get('/task/create', [ManagerController::class, 'createTask']);
    Route::post('/logout', [Managerontroller::class, 'logout']);

});

Route::middleware(['auth:sanctum', 'user'])->group(function () {
    Route::get('/user-only', function () {
        return "User";
    });
});
