<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ThesisController;
use App\Http\Controllers\ProfController;
use App\Http\Controllers\Auth\ActivationController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\SwaggerController;



Route::get('/password/reset', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
Route::post('/password/reset/request', [PasswordResetController::class, 'requestReset'])->name('password.request');
Route::get('/password/reset/verify', [PasswordResetController::class, 'showVerifyForm'])->name('password.verify');
Route::post('/password/reset/verify', [PasswordResetController::class, 'verifyReset'])->name('password.verify.submit');
Route::get('/password/reset/resend', [PasswordResetController::class, 'showResendForm'])->name('password.resend');
Route::post('/password/reset/resend', [PasswordResetController::class, 'resend'])->name('password.resend.submit');



Route::get('/activation', [ActivationController::class, 'show'])->name('activation.show');
Route::post('/activation/verify', [ActivationController::class, 'verify'])->name('activation.verify');
Route::get('/activation/resend', [ActivationController::class, 'showResendForm'])->name('resend.form');
Route::post('/activation/resend', [ActivationController::class, 'resend'])->name('activation.resend');



Route::get('/', [HomeController::class, 'welcome'])->name('welcome');
Route::get('/kontakt', function () {return view('kontakt');  })->name('kontakt');
Route::get('/prof/thesis/{id}', [ThesisController::class, 'show'])->name('thesis.show');
Route::get('/home', [HomeController::class, 'index'])->name('home');


Route::middleware(['guest'])->group(function () {
    Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register')->middleware('no-cache');
    Route::post('register', [RegisterController::class, 'register']);
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login')->middleware('no-cache');
    Route::post('login', [LoginController::class, 'login']);
});






Route::middleware(['auth'])->group(function () {


    Route::middleware(['role:Admin'])->get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin-dashboard');
    Route::middleware(['role:Admin'])->delete('/admin/delete-user/{id}', [AdminController::class, 'deleteUser'])->name('admin.delete-user');


    Route::middleware(['role:Prof'])->get('/prof/dashboard', [ThesisController::class, 'index'])->name('prof.dashboard');  
    Route::middleware(['role:Prof'])->get('/thesis/create', [ThesisController::class, 'create'])->name('thesis.create');
    Route::middleware(['role:Prof'])->post('/prof/thesis', [ThesisController::class, 'store'])->name('thesis.store'); 
    Route::middleware(['role:Prof'])->get('/prof/thesis/{id}/edit', [ThesisController::class, 'edit'])->name('thesis.edit');
    Route::middleware(['role:Prof'])->put('/prof/thesis/{id}', [ThesisController::class, 'update'])->name('thesis.update');
    Route::middleware(['role:Prof'])->get('/prof/edit', [ProfController::class, 'edit'])->name('prof.edit');
    Route::middleware(['role:Prof'])->put('/prof/update', [ProfController::class, 'update'])->name('prof.update');
    Route::middleware(['role:Prof'])->delete('/prof/thesis/{id}', [ThesisController::class, 'destroy'])->name('thesis.loeschen');
    Route::middleware(['role:Prof'])->get('/prof/geheimthesis', [ThesisController::class, 'geheimthesis'])->name('prof.geheimthesis');

    Route::middleware(['role:Prof'])->get('/prof/uebersicht', [ThesisController::class, 'uebersicht'])->name('prof.uebersicht');
    Route::middleware(['role:Prof'])->post('/thesis/upload-file', [ThesisController::class, 'uploadFile'])->name('thesis.upload-file');

    

    Route::middleware(['role:Student'])->get('/student/dashboard', function () {return view('helloStudent');})->name('student.dashboard');
    Route::middleware(['role:Student'])->post('/thesis/{id}/interesse', [ThesisController::class, 'interesse'])->name('thesis.interesse');
    Route::middleware(['role:Student'])->get('/student/merkliste', [ThesisController::class, 'merkliste'])->name('student.merkliste');

    

    Route::post('/logout', function () {Auth::logout();return redirect()->route('login')->with('success', 'Logged out successfully!');})->name('logout');



    Route::middleware(['role:Prof'])->get('/api/documentation', [SwaggerController::class, 'api'])->name('swagger.api');

    Route::post('/logout', function () {
        Auth::logout();
        return redirect()->route('login')->with('success', 'Logged out successfully!');
    })->name('logout');




});
