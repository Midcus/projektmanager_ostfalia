<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ThesisController;
use App\Http\Controllers\ProfController;

use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ActivationController;
use App\Http\Controllers\Auth\PasswordResetController;

use App\Http\Controllers\SwaggerController;

//Public

Route::controller(HomeController::class)->group(function () {
    Route::get('/', 'welcome')->name('welcome');
    Route::get('/home', 'index')->name('home');
});

Route::get('/kontakt', fn () => view('kontakt'))->name('kontakt');

// public thesis show 
Route::get('/prof/thesis/{id}', [ThesisController::class, 'show'])->name('thesis.show');

//Password Reset
Route::prefix('password/reset')
    ->name('password.')
    ->controller(PasswordResetController::class)
    ->group(function () {
        Route::get('/', 'showResetForm')->name('reset');
        Route::post('/request', 'requestReset')->name('request');

        Route::get('/verify', 'showVerifyForm')->name('verify');
        Route::post('/verify', 'verifyReset')->name('verify.submit');

        Route::get('/resend', 'showResendForm')->name('resend');
        Route::post('/resend', 'resend')->name('resend.submit');
    });

//Activation

Route::prefix('activation')
    ->name('activation.')
    ->controller(ActivationController::class)
    ->group(function () {
        Route::get('/', 'show')->name('show');
        Route::post('/verify', 'verify')->name('verify');

        Route::get('/resend', 'showResendForm')->name('resend.form');
        Route::post('/resend', 'resend')->name('resend');
    });

//Guest (Login/Register)

Route::middleware('guest')->group(function () {
    Route::controller(RegisterController::class)->group(function () {
        Route::get('/register', 'showRegistrationForm')->name('register')->middleware('no-cache');
        Route::post('/register', 'register');
    });

    Route::controller(LoginController::class)->group(function () {
        Route::get('/login', 'showLoginForm')->name('login')->middleware('no-cache');
        Route::post('/login', 'login');
    });
});

//Authenticated

Route::middleware('auth')->group(function () {

    // ✅ chỉ giữ 1 route logout (xóa route trùng)
    Route::post('/logout', function () {
        Auth::logout();
        return redirect()->route('login')->with('success', 'Logged out successfully!');
    })->name('logout');

    //Admin (role:Admin)

    Route::prefix('admin')
        ->name('admin.')
        ->middleware('role:Admin')
        ->controller(AdminController::class)
        ->group(function () {
            Route::get('/dashboard', 'dashboard')->name('dashboard');
            Route::delete('/delete-user/{id}', 'deleteUser')->name('delete-user');
        });

    // Prof (role:Prof)

    Route::prefix('prof')
        ->name('prof.')
        ->middleware('role:Prof')
        ->group(function () {

            // Thesis actions for Prof (controller grouped)
            Route::controller(ThesisController::class)->group(function () {
                Route::get('/dashboard', 'index')->name('dashboard');

                Route::get('/thesis/create', 'create')->name('thesis.create');
                Route::post('/thesis', 'store')->name('thesis.store');

                Route::get('/thesis/{id}/edit', 'edit')->name('thesis.edit');
                Route::put('/thesis/{id}', 'update')->name('thesis.update');

                Route::delete('/thesis/{id}', 'destroy')->name('thesis.delete');

                Route::get('/geheimthesis', 'geheimthesis')->name('geheimthesis');
                Route::get('/uebersicht', 'uebersicht')->name('uebersicht');

                Route::post('/thesis/upload-file', 'uploadFile')->name('thesis.upload-file');
            });

            // Prof profile
            Route::controller(ProfController::class)->group(function () {
                Route::get('/edit', 'edit')->name('edit');
                Route::put('/update', 'update')->name('update');
            });

            // Swagger (Prof only)
            Route::get('/api/documentation', [SwaggerController::class, 'api'])->name('swagger.api');
        });

    // Student (role:Student)

    Route::prefix('student')
        ->name('student.')
        ->middleware('role:Student')
        ->group(function () {
            Route::get('/dashboard', fn () => view('helloStudent'))->name('dashboard');

            // Student actions (dùng ThesisController nhưng vẫn giữ URL như bạn đang có)
            Route::controller(ThesisController::class)->group(function () {
                Route::post('/thesis/{id}/interesse', 'interesse')->name('thesis.interesse');
                Route::get('/merkliste', 'merkliste')->name('merkliste');
            });
        });
});
