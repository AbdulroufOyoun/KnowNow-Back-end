<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CollectionCodeController;
use App\Http\Controllers\CollectionController;
use App\Http\Controllers\CourseCodeController;
use App\Http\Controllers\CourseCollectionController;
use App\Http\Controllers\CourseCommentController;
use App\Http\Controllers\CourseContainController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\CourseDescriptionController;
use App\Http\Controllers\CourseDetailController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Auth
Route::post('/login', [AuthController::class, 'login']);
Route::post('/signUp', [AuthController::class, 'signUp']);

Route::group(
    ['middleware' => ['auth:api']],
    function () {

        // Course
        Route::get('/show_courses', [CourseController::class, 'index']);
        Route::get('/search_course', [CourseController::class, 'search']);
        Route::get('/find_course', [CourseController::class, 'find']);

        // Course Details
        Route::get('/show_course_details', [CourseDetailController::class, 'index']);

        // Course Description
        Route::get('/show_course_description', [CourseDescriptionController::class, 'index']);

        // Course Contain
        Route::get('/show_course_contain', [CourseContainController::class, 'index']);

        // Course Comments
        Route::get('/show_course_comments', [CourseCommentController::class, 'index']);
        Route::delete('/delete_course_comment', [CourseCommentController::class, 'destroy']);

        // Collection
        Route::get('/show_collections', [CollectionController::class, 'index']);
        Route::get('/search_collection', [CollectionController::class, 'search']);
        Route::get('/find_collection', [CollectionController::class, 'find']);

        // Collection Courses
        Route::get('/show_collection_courses', [CourseCollectionController::class, 'index']);
        Route::group(
            ['middleware' => ['role:superAdmin|admin']],
            function () {
                // Role
                Route::get('/make_doctor', [AuthController::class, 'makeDoctor']);
                Route::get('/show_doctors', [AuthController::class, 'Doctors']);

                // Course
                Route::get('/show_admin_courses', [CourseController::class, 'adminIndex']);
                Route::get('/search_admin_course', [CourseController::class, 'adminSearch']);
                Route::get('/find_admin_course', [CourseController::class, 'adminFind']);
                Route::post('/add_course', [CourseController::class, 'store']);
                Route::delete('/delete_course', [CourseController::class, 'destroy']);

                // Course Details
                Route::post('/add_course_detail', [CourseDetailController::class, 'store']);
                Route::delete('/delete_course_detail', [CourseDetailController::class, 'destroy']);

                // Course Description
                Route::post('/add_course_description', [CourseDescriptionController::class, 'store']);
                Route::delete('/delete_course_description', [CourseDescriptionController::class, 'destroy']);

                // Course Contain
                Route::post('/add_course_contain', [CourseContainController::class, 'store']);
                Route::delete('/delete_course_contain', [CourseContainController::class, 'destroy']);

                // Course Comments
                Route::post('/add_course_comment', [CourseCommentController::class, 'store']);

                // Course Codes
                Route::get('/show_course_codes', [CourseCodeController::class, 'index']);
                Route::get('/show_all_course_codes', [CourseCodeController::class, 'indexAll']);
                Route::get('/specific_course_codes', [CourseCodeController::class, 'show']);
                Route::post('/add_course_code', [CourseCodeController::class, 'store']);
                Route::delete('/delete_course_code', [CourseCodeController::class, 'destroy']);


                // Collection
                Route::get('/show_admin_collections', [CollectionController::class, 'adminIndex']);
                Route::get('/search_admin_collection', [CollectionController::class, 'adminSearch']);
                Route::get('/find_admin_collection', [CollectionController::class, 'adminFind']);
                Route::post('/add_collection', [CollectionController::class, 'store']);
                Route::delete('/delete_collection', [CollectionController::class, 'destroy']);

                // Collection Courses
                Route::post('/add_collection_courses', [CourseCollectionController::class, 'store']);
                Route::get('/show_admin_collection_courses', [CourseCollectionController::class, 'adminIndex']);
                Route::delete('/delete_collection_courses', [CourseCollectionController::class, 'destroy']);

                // Collection Codes
                Route::get('/show_collection_codes', [CollectionCodeController::class, 'index']);
                Route::get('/show_all_collection_codes', [CollectionCodeController::class, 'indexAll']);
                Route::get('/specific_collection_codes', [CollectionCodeController::class, 'show']);
                Route::post('/add_collection_code', [CollectionCodeController::class, 'store']);
                Route::delete('/delete_collection_code', [CollectionCodeController::class, 'destroy']);
            }
        );

        // Auth
        Route::delete('/logout', [AuthController::class, 'logout']);
    }
);

Route::get('login_error', [AuthController::class, 'loginError'])->name('login');


Route::get('/video/secret/{key}', [CourseContainController::class, 'getSecretKey'])->name('api.video.key');

Route::get('/video/{playlist}', [CourseContainController::class, 'getPlaylist'])->name('api.video.playlist');

Route::get('/pdf/{pdfName}', [CourseContainController::class, 'getPdf'])->name('api.pdf');