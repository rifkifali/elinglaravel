<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\KabagController;
use App\Http\Controllers\KasubbagController;
use App\Http\Controllers\PerancangController;
use App\Http\Controllers\SuperAdminController;

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Admin TU Routes
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:Admin'])->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('index');
    Route::get('/input-surat', [AdminController::class, 'createSurat'])->name('input-surat');
    Route::post('/input-surat', [AdminController::class, 'storeSurat'])->name('store-surat');
});

/*
|--------------------------------------------------------------------------
| Kabag Routes
|--------------------------------------------------------------------------
*/

Route::prefix('kabag')->name('kabag.')->middleware(['auth', 'role:Kabag'])->group(function () {
    Route::get('/', [KabagController::class, 'index'])->name('index');
    Route::get('/disposisi/{id}', [KabagController::class, 'disposisi'])->name('disposisi');
    Route::post('/disposisi/{id}', [KabagController::class, 'storeDisposisi'])->name('store-disposisi');
    Route::get('/detail/{id}', [KabagController::class, 'detailLacak'])->name('detail');
    Route::post('/finalisasi/{id}', [KabagController::class, 'finalisasi'])->name('finalisasi');
    Route::post('/revisi/{id}', [KabagController::class, 'revisi'])->name('revisi');
});

/*
|--------------------------------------------------------------------------
| Kasubbag Routes
|--------------------------------------------------------------------------
*/

Route::prefix('kasubbag')->name('kasubbag.')->middleware(['auth', 'role:Kasubbag'])->group(function () {
    Route::get('/', [KasubbagController::class, 'index'])->name('index');
    Route::get('/review/{id}', [KasubbagController::class, 'reviewDraf'])->name('review');
    Route::post('/revisi/{id}', [KasubbagController::class, 'revisi'])->name('revisi');
    Route::post('/setuju/{id}', [KasubbagController::class, 'setuju'])->name('setuju');
    Route::get('/detail/{id}', [KasubbagController::class, 'detailLacak'])->name('detail');
});

/*
|--------------------------------------------------------------------------
| Perancang Routes
|--------------------------------------------------------------------------
*/

Route::prefix('perancang')->name('perancang.')->middleware(['auth', 'role:Perancang'])->group(function () {
    Route::get('/', [PerancangController::class, 'index'])->name('index');
    Route::get('/workspace/{id}', [PerancangController::class, 'workspace'])->name('workspace');
    Route::post('/workspace/{id}', [PerancangController::class, 'kirimDraf'])->name('kirim-draf');
});

/*
|--------------------------------------------------------------------------
| Super Admin Routes
|--------------------------------------------------------------------------
*/

Route::prefix('superadmin')->name('superadmin.')->middleware(['auth', 'role:Super Admin'])->group(function () {
    // Users
    Route::get('/', [SuperAdminController::class, 'index'])->name('index');
    Route::get('/user/form', [SuperAdminController::class, 'formUser'])->name('form-user');
    Route::post('/user/form', [SuperAdminController::class, 'saveUser'])->name('save-user');
    Route::get('/user/form/{id}', [SuperAdminController::class, 'formUser'])->name('edit-user');
    Route::post('/user/form/{id}', [SuperAdminController::class, 'saveUser'])->name('update-user');
    Route::delete('/user/{id}', [SuperAdminController::class, 'deleteUser'])->name('delete-user');

    // Surat
    Route::get('/manage-surat', [SuperAdminController::class, 'manageSurat'])->name('manage-surat');
    Route::get('/manage-surat/edit/{id}', [SuperAdminController::class, 'editSurat'])->name('edit-surat');
    Route::post('/manage-surat/edit/{id}', [SuperAdminController::class, 'updateSurat'])->name('update-surat');
    Route::delete('/manage-surat/{id}', [SuperAdminController::class, 'deleteSurat'])->name('delete-surat');

    // Timeline
    Route::get('/manage-timeline', [SuperAdminController::class, 'manageTimeline'])->name('manage-timeline');
    Route::get('/manage-timeline/edit/{id}', [SuperAdminController::class, 'editTimeline'])->name('edit-timeline');
    Route::post('/manage-timeline/edit/{id}', [SuperAdminController::class, 'updateTimeline'])->name('update-timeline');
    Route::delete('/manage-timeline/{id}', [SuperAdminController::class, 'deleteTimeline'])->name('delete-timeline');
});