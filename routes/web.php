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
use App\Http\Controllers\SeoController;

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
use App\Http\Controllers\Admin\CompetitionController;

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
Route::get('/event/{slug}', [LandingPageController::class, 'eventDetail'])->name('frontend.events.show');

Route::get('/robots.txt', [SeoController::class, 'robots'])->name('seo.robots');
Route::get('/sitemap.xml', [SeoController::class, 'sitemap'])->name('seo.sitemap');

Route::middleware(['auth'])->group(function () {
    Route::get('/my-event', [LandingPageController::class, 'myEvents'])->name('frontend.my-events');
    Route::get('/my-event/attendance', [LandingPageController::class, 'attendanceScanner'])->name('frontend.attendance.scan');
    Route::post('/my-event/attendance', [LandingPageController::class, 'submitAttendance'])->name('frontend.attendance.submit');
    Route::get('/sertifikat', [LandingPageController::class, 'certificates'])->name('frontend.certificates');
    Route::post('/event/{slug}/register', [LandingPageController::class, 'registerEvent'])->name('frontend.events.register');

    // === MY TEAMS (Mahasiswa — untuk Lomba) ===
    Route::get('/my-teams', [\App\Http\Controllers\TeamController::class, 'index'])->name('frontend.teams.index');
    Route::get('/my-teams/create', [\App\Http\Controllers\TeamController::class, 'create'])->name('frontend.teams.create');
    Route::post('/my-teams', [\App\Http\Controllers\TeamController::class, 'store'])->name('frontend.teams.store');
    Route::delete('/my-teams/{team}', [\App\Http\Controllers\TeamController::class, 'destroy'])->name('frontend.teams.destroy');
    Route::get('/my-teams/{team}/kelola', [\App\Http\Controllers\TeamController::class, 'manage'])->name('frontend.teams.manage');
    Route::post('/my-teams/{team}/invite', [\App\Http\Controllers\TeamController::class, 'invite'])->name('frontend.teams.invite');
    Route::delete('/my-teams/{team}/anggota/{member}', [\App\Http\Controllers\TeamController::class, 'removeMember'])->name('frontend.teams.member.remove');
    Route::post('/team-invite/{member}/accept', [\App\Http\Controllers\TeamController::class, 'acceptInvite'])->name('frontend.team-invite.accept');
    Route::post('/team-invite/{member}/reject', [\App\Http\Controllers\TeamController::class, 'rejectInvite'])->name('frontend.team-invite.reject');

    Route::get('/my-event/{slug}', [LandingPageController::class, 'myEventDetail'])->name('frontend.my-event.show');
    Route::delete('/my-event/{slug}/cancel', [LandingPageController::class, 'cancelRegistration'])->name('frontend.my-event.cancel');
    Route::post('/my-event/{slug}/documents/{requirement}', [\App\Http\Controllers\ParticipantDocumentController::class, 'upload'])->name('frontend.my-event.documents.upload');
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

    Route::middleware(['role:baak,kemahasiswaan,superuser,penanggung_jawab,ketua_pelaksana,panitia'])->group(function () {
        // === DASHBOARD UTAMA ===
        // Admin & Pengurus (Penanggung Jawab, Ketua Pelaksana, Panitia) boleh masuk area dashboard admin
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

// Manajemen Role (Superuser only)
Route::middleware(['auth', 'role:superuser'])->prefix('admin')->group(function () {
    Route::resource('role-management', \App\Http\Controllers\Admin\RoleManagementController::class)->except(['show']);
});

/*
|--------------------------------------------------------------------------
| Admin Routes (Shared Event Access)
|--------------------------------------------------------------------------
| Role: BAAK, Kemahasiswaan, Super User, Penanggung Jawab
| Penanggung Jawab dibatasi hanya modul Data Event + Sertifikat
*/
Route::middleware(['auth', 'role:baak,kemahasiswaan,superuser,penanggung_jawab'])->prefix('admin')->group(function () {
    Route::get('events', [AdminEventController::class, 'index'])->name('events.index');
    Route::get('events/create', [AdminEventController::class, 'create'])->name('events.create');
    Route::post('events', [AdminEventController::class, 'store'])->name('events.store');
    Route::delete('events/{event}', [AdminEventController::class, 'destroy'])->name('events.destroy');

    // Generate QR Event (dari halaman Data Event)
    Route::get('events/{slug}/generate-qr', [AdminQrController::class, 'generate'])->name('admin.events.qr');
    Route::post('events/{event}/generate-qr/refresh', [AdminQrController::class, 'refresh'])->name('admin.events.qr.refresh');
});

// Edit/Update dibuka juga untuk Ketua Pelaksana (khusus lomba yang ditugaskan padanya) —
// otorisasi granular tetap dicek di AdminEventController::authorizeEventByRole().
Route::middleware(['auth', 'role:baak,kemahasiswaan,superuser,penanggung_jawab,ketua_pelaksana'])->prefix('admin')->group(function () {
    Route::get('events/{event}/edit', [AdminEventController::class, 'edit'])->name('events.edit');
    Route::put('events/{event}', [AdminEventController::class, 'update'])->name('events.update');
});

/*
|--------------------------------------------------------------------------
| Lomba / Perlombaan — Manajemen & Panitia
|--------------------------------------------------------------------------
| Role: BAAK, Kemahasiswaan, Superuser, Penanggung Jawab, Ketua Pelaksana
| (otorisasi granular per-lomba ditangani di CompetitionController)
*/
Route::middleware(['auth', 'role:baak,kemahasiswaan,superuser,penanggung_jawab,ketua_pelaksana'])
    ->prefix('admin')->group(function () {
        Route::get('lomba', [CompetitionController::class, 'index'])->name('lomba.index');
        Route::get('lomba/{event}/kelola', [CompetitionController::class, 'manage'])->name('lomba.manage');
        Route::get('lomba/search-mahasiswa', [CompetitionController::class, 'searchMahasiswa'])->name('lomba.search-mahasiswa');
        Route::post('lomba/{event}/ketua', [CompetitionController::class, 'assignKetua'])->name('lomba.assign-ketua');
        Route::post('lomba/{event}/panitia', [CompetitionController::class, 'storePanitia'])->name('lomba.panitia.store');
        Route::delete('lomba/{event}/panitia/{committee}', [CompetitionController::class, 'destroyPanitia'])->name('lomba.panitia.destroy');

        Route::get('lomba/{event}/cabang/create', [CompetitionController::class, 'createBranch'])->name('lomba.cabang.create');
        Route::post('lomba/{event}/cabang', [CompetitionController::class, 'storeBranch'])->name('lomba.cabang.store');
        Route::get('lomba/{event}/cabang/{branch}/edit', [CompetitionController::class, 'editBranch'])->name('lomba.cabang.edit');
        Route::put('lomba/{event}/cabang/{branch}', [CompetitionController::class, 'updateBranch'])->name('lomba.cabang.update');
        Route::delete('lomba/{event}/cabang/{branch}', [CompetitionController::class, 'destroyBranch'])->name('lomba.cabang.destroy');

        Route::post('lomba/{event}/dokumen', [CompetitionController::class, 'storeDocumentRequirement'])->name('lomba.dokumen.store');
        Route::put('lomba/{event}/dokumen/{requirement}', [CompetitionController::class, 'updateDocumentRequirement'])->name('lomba.dokumen.update');
        Route::delete('lomba/{event}/dokumen/{requirement}', [CompetitionController::class, 'destroyDocumentRequirement'])->name('lomba.dokumen.destroy');
        Route::post('lomba/{event}/dokumen/{requirement}/reorder', [CompetitionController::class, 'reorderDocumentRequirement'])->name('lomba.dokumen.reorder');

        // Kriteria Penilaian
        Route::get('lomba/{event}/kriteria', [CompetitionController::class, 'kriteria'])->name('lomba.kriteria.index');
        Route::post('lomba/{event}/kriteria', [CompetitionController::class, 'storeAssessmentCriterion'])->name('lomba.kriteria.store');
        Route::put('lomba/{event}/kriteria/{criterion}', [CompetitionController::class, 'updateAssessmentCriterion'])->name('lomba.kriteria.update');
        Route::delete('lomba/{event}/kriteria/{criterion}', [CompetitionController::class, 'destroyAssessmentCriterion'])->name('lomba.kriteria.destroy');

        // Skoring / Penilaian
        Route::get('lomba/{event}/penilaian', [\App\Http\Controllers\Admin\ScoreController::class, 'index'])->name('lomba.penilaian');
        Route::post('lomba/{event}/penilaian/{registration}', [\App\Http\Controllers\Admin\ScoreController::class, 'store'])->name('lomba.penilaian.store');
        Route::get('lomba/{event}/leaderboard', [\App\Http\Controllers\Admin\ScoreController::class, 'leaderboard'])->name('lomba.leaderboard');

        Route::get('lomba/{event}/berkas', [CompetitionController::class, 'documentReview'])->name('lomba.berkas');
        Route::post('lomba/{event}/berkas/{document}/review', [CompetitionController::class, 'reviewDocument'])->name('lomba.berkas.review');

        Route::get('lomba/{event}/export/peserta/excel', [CompetitionController::class, 'exportPesertaExcel'])->name('lomba.export.peserta.excel');
        Route::get('lomba/{event}/export/peserta/pdf', [CompetitionController::class, 'exportPesertaPdf'])->name('lomba.export.peserta.pdf');

        Route::get('lomba/{event}/juara', [CompetitionController::class, 'juara'])->name('lomba.juara');
        Route::post('lomba/{event}/juara', [CompetitionController::class, 'storeJuara'])->name('lomba.juara.store');
        Route::delete('lomba/{event}/juara/{juara}', [CompetitionController::class, 'destroyJuara'])->name('lomba.juara.destroy');
        Route::get('lomba/{event}/export/juara/pdf', [CompetitionController::class, 'exportJuaraPdf'])->name('lomba.export.juara.pdf');

        Route::get('lomba/{event}/certificates', [\App\Http\Controllers\Admin\LombaCertificateController::class, 'manage'])->name('lomba.certificates.manage');
        Route::get('lomba/{event}/certificates/{jenis}/designer', [\App\Http\Controllers\Admin\LombaCertificateController::class, 'designer'])->name('lomba.certificates.designer');
        Route::get('lomba/{event}/certificates/template', [\App\Http\Controllers\Admin\LombaCertificateController::class, 'templateShow'])->name('lomba.certificates.template.show');
        Route::post('lomba/{event}/certificates/template', [\App\Http\Controllers\Admin\LombaCertificateController::class, 'templateStore'])->name('lomba.certificates.template.store');
    });

Route::middleware(['auth', 'role:baak,kemahasiswaan,superuser,penanggung_jawab,ketua_pelaksana'])
    ->prefix('admin/api')->group(function () {
        Route::get('lomba-certificates/{event}/{jenis}/participants', [\App\Http\Controllers\Admin\LombaCertificateController::class, 'apiParticipants'])->name('lomba.certificates.api.participants');
        Route::post('lomba-certificates/upload', [\App\Http\Controllers\Admin\LombaCertificateController::class, 'uploadGenerated'])->name('lomba.certificates.api.upload');
    });

/*
|--------------------------------------------------------------------------
| Bendahara — Kelola Pembayaran Lomba
|--------------------------------------------------------------------------
| Akses ditentukan sepenuhnya di controller (Event::canBeManagedByBendahara(),
| berbasis Kepanitiaan jabatan "Bendahara Lomba"), BUKAN oleh role user — jadi
| middleware di sini cukup memastikan user sudah login.
*/
Route::middleware(['auth'])
    ->prefix('admin')->group(function () {
        Route::get('payment', [\App\Http\Controllers\Admin\PaymentController::class, 'index'])->name('payment.index');
        Route::get('payment/{event}', [\App\Http\Controllers\Admin\PaymentController::class, 'show'])->name('payment.show');
        Route::post('payment/{event}/{document}/verify', [\App\Http\Controllers\Admin\PaymentController::class, 'verify'])->name('payment.verify');
        Route::get('payment/{event}/export/excel', [\App\Http\Controllers\Admin\PaymentController::class, 'exportExcel'])->name('payment.export.excel');
        Route::get('payment/{event}/export/pdf', [\App\Http\Controllers\Admin\PaymentController::class, 'exportPdf'])->name('payment.export.pdf');
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
