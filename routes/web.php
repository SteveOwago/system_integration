<?php

use App\Http\Controllers\CourseController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();


Route::group(['middleware' => 'auth'], function () {
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::resource('users', UserController::class);
    Route::post('courses/payment/{course_id}', [CourseController::class, 'payment'])->name('courses.payment');
    Route::post('courses/enroll', [CourseController::class, 'enroll'])->name('courses.enroll');
    Route::resource('courses', CourseController::class);
});
