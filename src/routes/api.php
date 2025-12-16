<?php

use DET\Members\Http\Controllers\MemberController;
use DET\Members\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::prefix('api/v1')->middleware(['api', 'auth:sanctum'])->group(function () {

    // --- Profile Endpoints ---
    Route::prefix('members/profile')->group(function () {
        Route::get('/', [ProfileController::class, 'show']);
        Route::put('/', [ProfileController::class, 'update']); // Maps to 'update'
        Route::post('/avatar', [ProfileController::class, 'uploadAvatar']); // Maps to 'uploadAvatar'
    });

    // --- Member Management (Admin) ---
    Route::prefix('members')->group(function () {
        Route::get('/', [MemberController::class, 'index'])->middleware('can:member.view');
        Route::post('/', [MemberController::class, 'store'])->middleware('can:member.create');
        Route::put('/{id}', [MemberController::class, 'update'])->middleware('can:member.edit');
        Route::delete('/{id}', [MemberController::class, 'destroy'])->middleware('can:member.delete');

        // Custom Actions
        Route::post('/{id}/status', [MemberController::class, 'toggleStatus'])->middleware('can:member.edit');

        Route::post('/{id}/assign-role', [MemberController::class, 'assignRole'])->middleware('can:member.manage');
        Route::post('/{id}/remove-role', [MemberController::class, 'removeRole'])->middleware('can:member.manage');
    });
});
