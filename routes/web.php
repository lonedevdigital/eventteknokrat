<?php

use App\Http\Controllers\Admin\DashboardController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Import Controllers (Centralized)
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicQrController;

// Admin Namespace
use App\Http\Controllers\Admin\AdminEventController;
use App\Http\Controllers\Admin\EventCategoryController;
use App\Http\Controllers\Admin\MahasiswaController;
use App\Http\Controllers\Admin\InfoController;
use App\Http\Controllers\Admin\DocumentationController;
use App\Http\Controllers\Admin\AdminQrController;
use App\Http\Controllers\Admin\DashboardAnalyticsController;
use App\Http\Controllers\Admin\EventAnalyticsController;
use App\Http\Controllers\Admin\CertificateAdminController;
use App\Http\Controllers\Admin\CertificateTemplateController;
use App\Http\Controllers\Admin\EventAttendanceApiController;
use App\Http\Controllers\Admin\EventRecommendationController;
use App\Http\Controllers\Admin\SponsorController;

/*
|--------------------------------------------------------------------------
| Utility Routes
|--------------------------------------------------------------------------
*/

Route::get('/clear-cache', function () {
    Artisan::call('cache:clear');
    Artisan::call('route:clear');
    return 'Cache cleared successfully';
});

Route::get('/debug-user', function () {
    return [
        'logged_in' => auth()->check(),
        'user'      => auth()->user(),
        'role'      => auth()->user()->role ?? null,
    ];
});

Route::get('/', [LandingPageController::class, 'index'])->name('frontend.home');
Route::get('/event-list', [LandingPageController::class, 'events'])->name('frontend.events');
Route::get('/event-list/{slug}', [LandingPageController::class, 'eventDetail'])->name('frontend.events.show');

Route::middleware(['auth'])->group(function () {
    Route::get('/my-event', [LandingPageController::class, 'myEvents'])->name('frontend.my-events');
    Route::get('/my-event/attendance', [LandingPageController::class, 'attendanceScanner'])->name('frontend.attendance.scan');
    Route::post('/my-event/attendance', [LandingPageController::class, 'submitAttendance'])->name('frontend.attendance.submit');
    Route::get('/sertifikat', [LandingPageController::class, 'certificates'])->name('frontend.certificates');
    Route::post('/event-list/{slug}/register', [LandingPageController::class, 'registerEvent'])->name('frontend.events.register');
});

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
// QR Presensi bisa diakses tanpa login
Route::get('/presensi/qr/{qr_token}', [PublicQrController::class, 'show'])->name('public.qr.show');

Route::middleware(['auth', 'role:baak,kemahasiswaan,superuser'])
    ->get('/dashboard/export-pdf', [DashboardController::class, 'exportPdf'])
    ->name('dashboard.export-pdf');
/*
|--------------------------------------------------------------------------
| Authenticated Routes (General)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    // === PROFILE ===
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::middleware(['role:baak,kemahasiswaan,superuser,penanggung_jawab'])->group(function () {
        // === DASHBOARD UTAMA ===
        // Penanggung Jawab juga boleh masuk area dashboard admin
        Route::get('/dashboard', [DashboardAnalyticsController::class, 'index'])->name('dashboard');

        // === MANAJEMEN SERTIFIKAT (View) ===
        // Role Penanggung Jawab hanya diizinkan ke Data Event + Sertifikat
        Route::prefix('dashboard/certificates')->name('certificates.')->group(function () {
            Route::get('/', [CertificateAdminController::class, 'index'])->name('index');
            Route::get('/{event_id}', [CertificateAdminController::class, 'eventDetail'])->name('event-detail');
            Route::delete('/{registration_id}/delete-certificate', [CertificateAdminController::class, 'deleteCertificate'])->name('delete');
        });
    });

    Route::middleware(['role:baak,kemahasiswaan,superuser'])->group(function () {
        // Fitur Export Rekap Global
        Route::get('/dashboard/rekap/export-pdf', [HomeController::class, 'exportRekapPdf'])->name('dashboard.rekap.pdf');

        // === EVENT ANALYTICS (Detail & Export per Event) ===
        Route::prefix('dashboard/events')->name('events.')->group(function () {
            Route::get('completed', [EventAnalyticsController::class, 'completed'])->name('completed');
            Route::get('{id}/detail', [EventAnalyticsController::class, 'detail'])->name('detail');
            Route::get('{id}/export-excel', [EventAnalyticsController::class, 'exportExcel'])->name('export.excel');
            Route::get('{id}/export-pdf', [EventAnalyticsController::class, 'exportPDF'])->name('export.pdf');
        });
    });
});


/*
|--------------------------------------------------------------------------
| Internal API Routes (Auth Required)
|--------------------------------------------------------------------------
| Digunakan oleh JavaScript (AJAX/Fetch) di Frontend
*/
Route::prefix('admin/api')->middleware(['auth'])->group(function () {

    // API: Generator Sertifikat (Canvas Data & Upload)
    Route::get('certificates/event/{id}', [CertificateAdminController::class, 'apiEventParticipants']);
    Route::post('certificates/upload',    [CertificateAdminController::class, 'uploadGenerated']);

    // API: Absensi Manual & Tambah Peserta
    Route::post('/event-registrations/{registration}/attendance', [EventAttendanceApiController::class, 'setAttendance']);
    Route::post('/events/{event}/participants/add', [EventAttendanceApiController::class, 'addParticipant']);

    // API: Template Sertifikat (CRUD Layout JSON)
    Route::get('/events/{event}/certificate-templates', [CertificateTemplateController::class, 'index'])
        ->name('certificates.templates');
    Route::get('/events/{event}/certificate-template',  [CertificateTemplateController::class, 'show'])
        ->name('certificates.show_template');
    Route::post('/events/{event}/certificate-template', [CertificateTemplateController::class, 'store'])
        ->name('certificates.store_template'); // <--- INI PERBAIKAN PENTING
});


/*
|--------------------------------------------------------------------------
| Admin Routes (Admin Core)
|--------------------------------------------------------------------------
| Role: BAAK, Kemahasiswaan, Super User
*/
Route::middleware(['auth', 'role:baak,kemahasiswaan,superuser'])->prefix('admin')->group(function () {
    // Manajemen Event (khusus admin core)
    Route::get('events/recommendations', [EventRecommendationController::class, 'index'])
        ->name('events.recommendations.index');
    Route::post('events/recommendations/{event}', [EventRecommendationController::class, 'toggle'])
        ->name('events.recommendations.toggle');

    // Data master event
    Route::resource('event-categories', EventCategoryController::class)->names('event-categories');

    // Info Terkini
    Route::resource('infos', InfoController::class)->names('infos');
    Route::resource('sponsors', SponsorController::class)->except(['show'])->names('sponsors');

    // Dokumentasi Event
    Route::get('/documentations', [DocumentationController::class, 'index'])->name('documentations.index');
    Route::get('/documentations/create/{event_id}', [DocumentationController::class, 'create'])->name('documentations.create');
    Route::post('/documentations/store', [DocumentationController::class, 'store'])->name('documentations.store');
    Route::delete('/documentations/{id}', [DocumentationController::class, 'destroy'])->name('documentations.destroy');

    // Manajemen Mahasiswa (Sync & Reset Pass)
    Route::get('mahasiswa', [MahasiswaController::class, 'index'])->name('mahasiswa.index');
    Route::post('mahasiswa/sync', [MahasiswaController::class, 'store'])->name('mahasiswa.sync');
    Route::patch('mahasiswa/{id}/reset-password', [MahasiswaController::class, 'update'])->name('mahasiswa.reset-password');
    
    // Manajemen User (Admin & BAAK/Kemahasiswaan)
    Route::resource('user-management', \App\Http\Controllers\Admin\UserManagementController::class);
});

/*
|--------------------------------------------------------------------------
| Admin Routes (Shared Event Access)
|--------------------------------------------------------------------------
| Role: BAAK, Kemahasiswaan, Super User, Penanggung Jawab
| Penanggung Jawab dibatasi hanya modul Data Event + Sertifikat
*/
Route::middleware(['auth', 'role:baak,kemahasiswaan,superuser,penanggung_jawab'])->prefix('admin')->group(function () {
    Route::resource('events', AdminEventController::class)->names('events');

    // Generate QR Event (dari halaman Data Event)
    Route::get('events/{slug}/generate-qr', [AdminQrController::class, 'generate'])->name('admin.events.qr');
    Route::post('events/{event}/generate-qr/refresh', [AdminQrController::class, 'refresh'])->name('admin.events.qr.refresh');
});

// Route akses data master (via URL berbeda tapi controller sama)
Route::middleware(['auth', 'role:baak,kemahasiswaan,superuser'])
    ->get('/master/mahasiswa', [MahasiswaController::class, 'index'])
    ->name('data-mahasiswa.index');


/*
|--------------------------------------------------------------------------
| Auth & Additional File Imports
|--------------------------------------------------------------------------
*/
require __DIR__ . '/auth.php';
require __DIR__ . '/admin.php';
require __DIR__ . '/user.php';
require __DIR__ . '/dosen.php';
