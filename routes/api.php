<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TechnologyController;
use App\Http\Controllers\TestimonialController;
use App\Http\Controllers\WorkController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
/**
 * Public routes 
 */

//Authorization
Route::post('register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);

//Works
Route::get('works', [WorkController::class, 'index']);
Route::get('works/search', [WorkController::class, 'search']);
Route::get('works/{id}', [WorkController::class, 'show']);

//Testimonials
Route::get('testimonials', [TestimonialController::class, 'index']);

//Technologies
Route::get('technologies', [TechnologyController::class, 'index']);
Route::get('technologies/{name}', [TechnologyController::class, 'show']);

//PROTECTED ROUTES
Route::group(['middleware' => ['auth:sanctum']], function() {
    Route::post('logout', [UserController::class, 'logout']);
    Route::post('works', [WorkController::class, 'store']);
    Route::put('works/{id}', [WorkController::class, 'update']);
    Route::delete('works/{id}', [WorkController::class, 'destroy']);
    Route::post('testimonials', [TestimonialController::class, 'store']);
    Route::put('testimonials/{id}', [TestimonialController::class, 'update']);
    Route::delete('testimonials/{id}', [TestimonialController::class, 'destroy']);
    Route::post('technologies', [TechnologyController::class, 'store']);
    Route::put('technologies/{id}', [TechnologyController::class, 'update']);
    Route::delete('technologies/{id}', [TechnologyController::class, 'destroy']);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
