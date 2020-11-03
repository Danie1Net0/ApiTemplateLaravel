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
 * API Version 1
 */
Route::prefix('v1')->group(function () {
    /**
     * Auth Routes
     */
    Route::prefix('auth')->group(function () {
        /**
         * Login Routes
         */
        Route::namespace('Auth')->group(function () {
            Route::post('login', 'LoginController@login');
            Route::post('logout', 'LoginController@logout');
        });

        /**
         * Password Reset Routes
         */
        Route::namespace('Auth')->group(function () {
            Route::post('forgot-password', 'ResetPasswordController@request');
            Route::put('reset-password', 'ResetPasswordController@reset');
        });

        /**
         * Registration Routes
         */
        Route::prefix('registration')->group(function () {
            /**
             * Registration Routes
             */
            Route::post('/', 'Users\UserController@store');

            /**
             * Verification Routes
             */
            Route::prefix('confirmation')->namespace('Auth')->group(function () {
                Route::post('/', 'VerificationController@confirmation');
                Route::post('resend-code', 'VerificationController@resendCode')->name('resend_verification');
            });
        });
    });

    /**
     * Access Control Routes
     */
    Route::namespace('AccessControl')->prefix('access-control')->group(function () {
        Route::apiResource('roles', 'RoleController');
        Route::apiResource('permissions', 'PermissionController')->only(['index', 'show']);
    });

    /**
     * Users Routes
     */
    Route::apiResource('users', 'Users\UserController');
});
