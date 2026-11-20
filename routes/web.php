<?php

use App\Http\Controllers\CourseController;
use App\Http\Controllers\Demo\AuthController;
use App\Http\Controllers\Demo\HelloController;
use App\Http\Controllers\Demo\TaskController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| The course
|--------------------------------------------------------------------------
*/
Route::get('/', [CourseController::class, 'home'])->name('home');
Route::get('/lessons/{slug}', [CourseController::class, 'show'])
    ->where('slug', '[a-z0-9-]+')
    ->name('lessons.show');
Route::post('/lessons/{slug}/complete', [CourseController::class, 'complete'])
    ->where('slug', '[a-z0-9-]+')
    ->name('lessons.complete');
Route::get('/search', [CourseController::class, 'search'])->name('search');
Route::post('/progress/reset', [CourseController::class, 'reset'])->name('progress.reset');

/*
|--------------------------------------------------------------------------
| Live demos: real features you can use, then read the code
|--------------------------------------------------------------------------
*/
Route::prefix('demos')->name('demo.')->group(function () {
    // Route parameters: optional {name?} + a constraint
    Route::get('/hello/{name?}', HelloController::class)->whereAlpha('name')->name('hello');

    // Tasks: a full resource controller + one extra action
    Route::patch('/tasks/{task}/toggle', [TaskController::class, 'toggle'])->name('tasks.toggle');
    Route::resource('tasks', TaskController::class);

    // Authentication by hand
    Route::middleware('guest')->group(function () {
        Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
        Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:10,1');
        Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
    });

    Route::middleware('auth')->group(function () {
        Route::get('/account', [AuthController::class, 'account'])->name('account');
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    });
});
