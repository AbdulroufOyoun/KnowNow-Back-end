<?php

use App\Http\Controllers\AdController;
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
use App\Http\Controllers\MediaController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\SpecializationController;
use App\Http\Controllers\SpecializationCourseController;
use App\Http\Controllers\UniversityController;
use App\Http\Controllers\UserCodeController;
use App\Http\Controllers\UserController;
use App\Models\CourseContain;
use App\Models\university;
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

// University
Route::get('/show_universities', [UniversityController::class, 'index']);
// Ad
Route::get('/show_ads', [AdController::class, 'index']);
        // Media
        Route::get('/show_media', [MediaController::class, 'index']);

Route::group(
    ['middleware' => ['auth:api']],
    function () {


        // Specialization
        Route::get('/show_specializations', [SpecializationController::class, 'index']);
        Route::get('/show_year_courses', [SpecializationCourseController::class, 'index']);
        Route::get('/show_specialization_years', [SpecializationCourseController::class, 'index']);
        Route::get('/show_years', [SpecializationCourseController::class, 'year']);


        // Course
        Route::get('/show_courses', [CourseController::class, 'index']);
        Route::get('/show_user_courses', [CourseController::class, 'userCourses']);

        Route::get('/search_course', [CourseController::class, 'search']);
        Route::get('/find_course', [CourseController::class, 'find']);

        // Course Details
        Route::get('/show_course_details', [CourseDetailController::class, 'index']);

        // Course Description
        Route::get('/show_course_description', [CourseDescriptionController::class, 'index']);

        // Course Contain
        Route::get('/show_course_contain', [CourseContainController::class, 'index']);
        Route::get('/show_courses_pdf', [CourseContainController::class, 'pdfs']);
        Route::get('/toggle_contain', [CourseContainController::class, 'toggle']);
        Route::get('/toggle_contain_activity', [CourseContainController::class, 'toggleActive']);


        // Course Comments
        Route::get('/show_course_comments', [CourseCommentController::class, 'index']);
        Route::delete('/delete_course_comment', [CourseCommentController::class, 'destroy']);

        // Collection
        Route::get('/show_collections', [CollectionController::class, 'index']);
        Route::get('/check_subscribe_collection', [CollectionController::class, 'checkSubscribe']);
        Route::get('/search_collection', [CollectionController::class, 'search']);
        Route::get('/find_collection', [CollectionController::class, 'find']);

        // Collection Courses
        Route::get('/show_collection_courses', [CourseCollectionController::class, 'index']);

        // User Codes
        Route::post('/add_user_code', [UserCodeController::class, 'store']);



        Route::group(
            ['middleware' => ['role:superAdmin|admin']],
            function () {
                // Role
                Route::post('/make_doctor', [AuthController::class, 'makeDoctor']);
                Route::get('/show_doctors', [AuthController::class, 'Doctors']);
                Route::post('/send_notification', [NotificationController::class, 'sendBroadcastToAllUsers']);

                // University
                Route::post('/add_university', [UniversityController::class, 'store']);
                Route::delete('/delete_university/{university}', [UniversityController::class, 'destroy']);

                //Specialization
                Route::get('/show_admin_specialization', [SpecializationCourseController::class, 'adminIndex']);
                Route::post('/add_specialization', [SpecializationController::class, 'store']);
                Route::get('/show_specialization_courses', [SpecializationCourseController::class, 'indexSpecializationCourses']);
                Route::get('/show_course_specializations', [SpecializationCourseController::class, 'indexCourseSpecializations']);
                Route::get('/show_allowed_specializations', [SpecializationCourseController::class, 'show']);
                Route::post('/add_specialization_courses', [SpecializationCourseController::class, 'store']);
                Route::post('/delete_specialization_course', [SpecializationCourseController::class, 'destroy']);

                Route::post('/update_specialization_courses', [SpecializationCourseController::class, 'update']);

                // Course
                Route::get('/show_admin_courses', [CourseController::class, 'adminIndex']);
                Route::get('/search_admin_course', [CourseController::class, 'adminSearch']);
                Route::get('/find_admin_course', [CourseController::class, 'adminFind']);
                Route::get('/toggle_course_status', [CourseController::class, 'toggleStatus']);
                Route::post('/add_course', [CourseController::class, 'store']);
                Route::post('/update_course', [CourseController::class, 'update']);

                Route::delete('/delete_course', [CourseController::class, 'destroy']);

                // Course Details
                Route::post('/add_course_detail', [CourseDetailController::class, 'store']);
                Route::delete('/delete_course_detail', [CourseDetailController::class, 'destroy']);

                // Course Description
                Route::post('/add_course_description', [CourseDescriptionController::class, 'store']);
                Route::delete('/delete_course_description', [CourseDescriptionController::class, 'destroy']);

                // Course Contain
                Route::post('/add_course_contain', [CourseContainController::class, 'store']);
                Route::post('/add_linked_contain', [CourseContainController::class, 'storeLinked']);
                Route::post('/update_course_contain', [CourseContainController::class, 'update']);
                Route::delete('/delete_course_contain', [CourseContainController::class, 'destroy']);

                // Course Comments
                Route::post('/add_course_comment', [CourseCommentController::class, 'store']);

                // Course Codes
                Route::get('/show_course_codes', [CourseCodeController::class, 'index']);
                Route::get('/show_course_Subscriptions', [CourseCodeController::class, 'courseSubscriptions']);
                Route::get('/show_all_course_codes', [CourseCodeController::class, 'indexAll']);
                Route::post('/show_course_barren', [CourseCodeController::class, 'barren']);
                Route::post('/show_doctor_barren', [CourseCodeController::class, 'doctorBarren']);
                Route::get('/specific_course_codes', [CourseCodeController::class, 'show']);
                Route::post('/add_course_code', [CourseCodeController::class, 'store']);
                Route::delete('/delete_course_code', [CourseCodeController::class, 'destroy']);

                // Collection
                Route::get('/show_admin_collections', [CollectionController::class, 'adminIndex']);
                Route::get('/search_admin_collection', [CollectionController::class, 'adminSearch']);
                Route::get('/find_admin_collection', [CollectionController::class, 'adminFind']);
                Route::post('/show_collection_barren', [CollectionController::class, 'barren']);

                Route::post('/add_collection', [CollectionController::class, 'store']);
                Route::post('/update_collection', [CollectionController::class, 'update']);
                Route::get('/toggle_collection', [CollectionController::class, 'toggle']);
                Route::delete('/delete_collection', [CollectionController::class, 'destroy']);

                // Collection Courses
                Route::post('/add_collection_courses', [CourseCollectionController::class, 'store']);
                Route::post('/update_collection_courses', [CourseCollectionController::class, 'update']);
                Route::get('/show_admin_collection_courses', [CourseCollectionController::class, 'adminIndex']);
                Route::delete('/delete_collection_courses', [CourseCollectionController::class, 'destroy']);

                // Collection Codes
                Route::get('/show_collection_codes', [CollectionCodeController::class, 'index']);
                Route::get('/show_all_collection_codes', [CollectionCodeController::class, 'indexAll']);
                Route::get('/specific_collection_codes', [CollectionCodeController::class, 'show']);
                Route::post('/add_collection_code', [CollectionCodeController::class, 'store']);
                Route::delete('/delete_collection_code', [CollectionCodeController::class, 'destroy']);

                // Users
                Route::get('/show_students', [UserController::class, 'students']);
                Route::get('/toggle_user', [UserController::class, 'toggleStatus']);

                // Media
                Route::post('/update_media/{media}', [MediaController::class, 'update']);

                // Ad
                Route::delete('/delete_ad/{ad}', [AdController::class, 'destroy']);
                Route::post('/add_ad', [AdController::class, 'store']);
            }
        );

        Route::group(
            ['middleware' => ['subscribed']],
            function () {
                Route::get('/video/{playlist}', [CourseContainController::class, 'showPlaylist']);

                Route::get('/pdf/{pdf}', [CourseContainController::class, 'getPdf'])->name('api.pdf');
            }
        );

        // Auth
        Route::delete('/logout', [AuthController::class, 'logout']);
        Route::post('/update_token', [AuthController::class, 'UpdateToken']);


        Route::post('/change_password', [AuthController::class, 'SelfChangePassword']);
    }


);


Route::get('login_error', [AuthController::class, 'loginError'])->name('login');