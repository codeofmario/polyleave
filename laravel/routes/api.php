<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    AuthController,
    GithubController,
    LeaveController,
    ModerationController
};
use App\Http\Middleware\{IsAuthenticated, IsModerator};



Route::get('/public-info', fn () => ['version' => '1.0']);
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login',    [AuthController::class, 'login']);
    Route::post('refresh',  [AuthController::class, 'refresh']);

    Route::prefix('github')->group(function () {
        Route::get ('url',      [GithubController::class, 'authUrl']);
        Route::post('callback', [GithubController::class, 'callback']);
    });

    Route::middleware(IsAuthenticated::class)->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get ('me',     [AuthController::class, 'me']);
    });
});


Route::middleware(IsAuthenticated::class)->group(function () {

    Route::get ('leaves', [LeaveController::class, 'index']);
    Route::post('leaves', [LeaveController::class, 'store']);

    Route::middleware(IsModerator::class)
        ->prefix('moderation')
        ->group(function () {

            Route::get ('leaves/pending',     [ModerationController::class, 'index']);
            Route::put('leaves/{leave}', [ModerationController::class, 'decide']);
        });

});
