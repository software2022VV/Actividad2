<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\Posts\PostsController;
use App\Http\Controllers\Categories\CategoriesController;
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
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//Auth Routes
Route::post('register', 'App\Http\Controllers\UserController@register');
Route::post('login', 'App\Http\Controllers\UserController@authenticate');



Route::group(['middleware' => ['jwt.verify']], function () {

    Route::get('user', [UserController::class,'getAuthenticatedUser'])->name('users.info');
    Route::get('users', [UserController::class,'indexC'])->name('users.index');
    Route::get('users/{id}/show', [UserController::class,'showC'])->name('users.show');
    Route::put('users/update', [UserController::class,'updateAuth'])->name('users.update-auth');
    Route::delete('users/{id}/delete', [UserController::class,'destroy'])->name('users.destroy');
    Route::post('users', [UserController::class,'storeC'])->name('users.store');

    //Categories Routes
    Route::resource('categories',CategoriesController::class);
    //Categories Routes
    Route::resource('posts',PostsController::class);
    Route::put('posts/{id}/publish',[PostsController::class,'updateState'])->name('posts.publish');
    //Users Routes

});
