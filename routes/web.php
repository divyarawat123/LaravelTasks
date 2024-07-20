<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

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

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/',[UserController::class,'index']);
Route::post('/form-submit',[UserController::class,'add'])->name('form.submit');
Route::get('/fetch-data',[UserController::class,'view'])->name('fetch.data');
Route::get('/roles',[UserController::class,'role']);
