<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CharacterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\StudentImportController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [PublicController::class, 'home'])->name('public.home');
Route::get('/layanan/{service}', [PublicController::class, 'service'])->name('public.service');
Route::get('/berita', [PublicController::class, 'news'])->name('public.news');
Route::get('/berita/{news:slug}', [PublicController::class, 'newsShow'])->name('public.news.show');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'loginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'registerForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::get('/siswa/template-import', [StudentImportController::class, 'template'])
        ->middleware('role:admin')
        ->name('students.import.template');
    Route::post('/siswa/import', [StudentImportController::class, 'import'])
        ->middleware('role:admin')
        ->name('students.import');
    Route::delete('/siswa', [StudentImportController::class, 'destroySelected'])
        ->middleware('role:admin')
        ->name('students.destroy-selected');
    Route::get('/absensi', [ModuleController::class, 'attendance'])->name('attendance');
    Route::get('/lms', [ModuleController::class, 'lms'])->name('lms');
    Route::get('/nilai', [ModuleController::class, 'grades'])->name('grades');
    Route::get('/komunikasi', [ModuleController::class, 'communication'])->name('communication');
    Route::get('/profil', [ModuleController::class, 'profile'])->name('profile');
    Route::get('/notifikasi', [NotificationController::class, 'index'])->name('notifications');

    Route::get('/karakter-sanksi', [CharacterController::class, 'index'])->name('character.index');
    Route::get('/karakter-sanksi/input', [CharacterController::class, 'create'])
        ->middleware('role:admin,guru')
        ->name('character.create');
    Route::post('/karakter-sanksi', [CharacterController::class, 'store'])
        ->middleware('role:admin,guru')
        ->name('character.store');
    Route::get('/karakter-sanksi/{student}/laporan', [CharacterController::class, 'report'])->name('character.report');

    Route::get('/berita-sekolah', [NewsController::class, 'index'])->name('news.index');
    Route::get('/berita-sekolah/tulis', [NewsController::class, 'create'])
        ->middleware('role:admin,guru')
        ->name('news.create');
    Route::post('/berita-sekolah', [NewsController::class, 'store'])
        ->middleware('role:admin,guru')
        ->name('news.store');
    Route::get('/berita-sekolah/{news:slug}', [NewsController::class, 'show'])->name('news.show');
});
