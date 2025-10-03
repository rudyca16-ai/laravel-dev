<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\EnrollmentController;
use Illuminate\Support\Facades\Route;


Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
});

Route::prefix('students')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/', [StudentController::class, 'index']);
    Route::post('/', [StudentController::class, 'store']);
    Route::get('/{student}', [StudentController::class, 'show']);
    Route::put('/{student}', [StudentController::class, 'update']);
    Route::delete('/{student}', [StudentController::class, 'destroy']);
});

Route::prefix('courses')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/', [CourseController::class, 'index']);
    Route::post('/', [CourseController::class, 'store']);
    Route::get('/{course}', [CourseController::class, 'show']);
    Route::put('/{course}', [CourseController::class, 'update']);
    Route::delete('/{course}', [CourseController::class, 'destroy']);
});

Route::prefix('enrollments')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/', [EnrollmentController::class, 'index']);
    Route::post('/', [EnrollmentController::class, 'store']);
    Route::delete('/{enrollment}', [EnrollmentController::class, 'destroy']);
});
