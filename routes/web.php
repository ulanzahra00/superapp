<?php

use App\Http\Controllers\AdminSchoolController;
use App\Http\Controllers\AdminUserController;
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
    Route::get('/{schoolSlug}/login', [AuthController::class, 'loginForm'])->name('school.login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'registerForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::post('/dashboard/tindak-lanjut/{student}', [DashboardController::class, 'respondFollowUp'])
        ->middleware('role:guru')
        ->name('dashboard.follow-up.respond');
    Route::get('/pengaturan/users', [AdminUserController::class, 'index'])
        ->middleware('role:admin')
        ->name('admin.users.index');
    Route::post('/pengaturan/users', [AdminUserController::class, 'store'])
        ->middleware('role:admin')
        ->name('admin.users.store');
    Route::get('/pengaturan/sekolah', [AdminSchoolController::class, 'index'])
        ->middleware('role:super_admin')
        ->name('admin.schools.index');
    Route::patch('/pengaturan/sekolah/{school}/setujui', [AdminSchoolController::class, 'approve'])
        ->middleware('role:super_admin')
        ->name('admin.schools.approve');
    Route::patch('/pengaturan/sekolah/{school}/tolak', [AdminSchoolController::class, 'reject'])
        ->middleware('role:super_admin')
        ->name('admin.schools.reject');
    Route::patch('/pengaturan/sekolah/{school}/nonaktifkan', [AdminSchoolController::class, 'deactivate'])
        ->middleware('role:super_admin')
        ->name('admin.schools.deactivate');
    Route::patch('/pengaturan/sekolah/{school}/aktifkan', [AdminSchoolController::class, 'reactivate'])
        ->middleware('role:super_admin')
        ->name('admin.schools.reactivate');
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
    Route::post('/lms/tugas', [ModuleController::class, 'storeLmsAssignment'])
        ->middleware('role:guru')
        ->name('lms.assignments.store');
    Route::post('/lms/tugas/{assignment}/jawaban', [ModuleController::class, 'submitLmsAssignment'])
        ->middleware('role:siswa')
        ->name('lms.assignments.submit');
    Route::post('/lms/jawaban/{submission}/nilai', [ModuleController::class, 'gradeLmsSubmission'])
        ->middleware('role:guru')
        ->name('lms.submissions.grade');
    Route::post('/lms/pengumuman', [ModuleController::class, 'sendLmsAnnouncement'])
        ->middleware('role:admin')
        ->name('lms.announcements.send');
    Route::get('/nilai', [ModuleController::class, 'grades'])->name('grades');
    Route::post('/nilai', [ModuleController::class, 'storeGrade'])
        ->middleware('role:guru')
        ->name('grades.store');
    Route::get('/komunikasi', [ModuleController::class, 'communication'])->name('communication');
    Route::post('/komunikasi/kirim', [ModuleController::class, 'sendMessage'])->name('communication.send');
    Route::post('/komunikasi/{message}/balas', [ModuleController::class, 'replyMessage'])->name('communication.reply');
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
    Route::get('/karakter-sanksi/surat-panggilan/{sanction}/download', [CharacterController::class, 'downloadSuratPanggilan'])
        ->name('character.surat-panggilan.download');
    Route::delete('/karakter-sanksi/point/{point}', [CharacterController::class, 'destroyPoint'])
        ->middleware('role:admin,guru')
        ->name('character.point.destroy');

    Route::get('/berita-sekolah', [NewsController::class, 'index'])->name('news.index');
    Route::get('/berita-sekolah/tulis', [NewsController::class, 'create'])
        ->middleware('role:admin,guru')
        ->name('news.create');
    Route::post('/berita-sekolah', [NewsController::class, 'store'])
        ->middleware('role:admin,guru')
        ->name('news.store');
    Route::get('/berita-sekolah/{news:slug}', [NewsController::class, 'show'])->name('news.show');
});

Route::get('/{schoolSlug}/layanan/{service}', [PublicController::class, 'service'])->name('public.school.service');
Route::get('/{schoolSlug}/berita', [PublicController::class, 'news'])->name('public.school.news');
Route::get('/{schoolSlug}/berita/{news:slug}', [PublicController::class, 'newsShow'])->name('public.school.news.show');
Route::get('/{schoolSlug}', [PublicController::class, 'home'])->name('public.school.home');
