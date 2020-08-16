<?php

use Illuminate\Support\Facades\Route;

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

/**
 * Auth Routes
 */
Route::prefix('auth')->namespace('Auth')->group(function () {
    /**
     * Login Routes
     */
    Route::post('login', 'LoginController@login');
    Route::post('logout', 'LoginController@logout');

    /**
     * Password Reset Routes
     */
    Route::prefix('password')->group(function () {
        Route::post('request', 'ResetPasswordController@request');
        Route::put('reset', 'ResetPasswordController@reset');
    });
});

/**
 * Registration Routes
 */
Route::prefix('registration')->group(function () {
    /**
     * Registration Routes
     */
    Route::post('users', 'Users\UserController@store');

    /**
     * Verification Routes
     */
    Route::prefix('verify')->namespace('Auth')->group(function () {
        Route::post('', 'VerificationController@verifyEmail');
        Route::post('resend', 'VerificationController@resend')->name('resend_verification');
        Route::get('token', 'VerificationController@verifyToken');
    });
});

/**
 * Access Control Routes
 */
Route::namespace('AccessControl')->group(function () {
    Route::resource('roles', 'RoleController');
    Route::resource('permissions', 'PermissionController')->only(['index', 'show']);
});

/**
 * Users Routes
 */
Route::namespace('Users')->group(function () {
    Route::resource('users', 'UserController');
});
