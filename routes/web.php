<?php

use App\Http\Controllers\CourseContainController;
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

// Route::group(['middleware' => ['auth', 'subscribed']], function () {
    Route::get('/vis/{playlist}', [CourseContainController::class, 'getPlaylist'])->name('web.video.playlist');
// });

Route::get('/vio/secret/{key}/{playlist}', [CourseContainController::class, 'getSecretKey'])->name('web.video.key');
