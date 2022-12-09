<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TechnologyController;
use App\Http\Controllers\TestimonialController;
use App\Http\Controllers\WorkController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EmailController;

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

//Testimonials (Website users can post as admin must approve)
Route::get('testimonials', [TestimonialController::class, 'index']);
Route::post('testimonials', [TestimonialController::class, 'store']);

//Technologies
Route::get('technologies', [TechnologyController::class, 'index']);
Route::get('technologies/{name}', [TechnologyController::class, 'show']);

//Email
Route::post('emailer', [EmailController::class, 'store']);

//PROTECTED ROUTES
//Route::group(['middleware' => ['auth:sanctum']], function() {
    Route::post('logout', [UserController::class, 'logout']);
    Route::post('works', [WorkController::class, 'store']);
    Route::put('works/{id}', [WorkController::class, 'update']);
    Route::delete('works/{id}', [WorkController::class, 'destroy']);
    Route::put('testimonials/{id}', [TestimonialController::class, 'update']);
    Route::delete('testimonials/{id}', [TestimonialController::class, 'destroy']);
    Route::post('technologies', [TechnologyController::class, 'store']);
    Route::put('technologies/{id}', [TechnologyController::class, 'update']);
    Route::delete('technologies/{id}', [TechnologyController::class, 'destroy']);
//});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
