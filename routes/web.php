<?php

// §8 — Route map / M1 Auth & RBAC / M2 Users Admin

use App\Http\Controllers\Admin\MonitoringController as AdminMonitoringController;
use App\Http\Controllers\Admin\SchoolClassController as AdminSchoolClassController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\GradeComponentController;
use App\Http\Controllers\GradeController;
use App\Http\Controllers\Guru\AttendanceController as GuruAttendanceController;
use App\Http\Controllers\Guru\MaterialController as GuruMaterialController;
use App\Http\Controllers\Guru\MeetingController as GuruMeetingController;
use App\Http\Controllers\Guru\QuizController as GuruQuizController;
use App\Http\Controllers\Guru\QuizQuestionController as GuruQuizQuestionController;
use App\Http\Controllers\Guru\SchoolClassController as GuruSchoolClassController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MaterialDownloadController;
use App\Http\Controllers\RecapController;
use App\Http\Controllers\Siswa\QuizController as SiswaQuizController;
use App\Http\Controllers\Siswa\SchoolClassController as SiswaSchoolClassController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// ─── Public landing page (FR-PUB-01) ─────────────────────────────────────────
Route::get('/', [HomeController::class, 'index'])->name('home');

// ─── Guest-only routes ────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    // FR-AUTH-01
    Route::get('/login', [LoginController::class, 'showForm'])->name('auth.login.show');
    Route::post('/login', [LoginController::class, 'login'])->name('auth.login');

    // FR-AUTH-02 / FR-AUTH-03 (siswa self-register only)
    Route::get('/register', [RegisterController::class, 'showForm'])->name('auth.register.show');
    Route::post('/register', [RegisterController::class, 'register'])->name('auth.register');

    // FR-AUTH-04 / BR-02
    Route::get('/forgot-password', [ForgotPasswordController::class, 'showForm'])->name('auth.forgot.show');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendEmail'])->name('auth.forgot.email');

    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showForm'])->name('auth.reset.show');
    Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('auth.reset');
});

// Logout (authenticated users only)
Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('auth.logout');
// ponytail: GET fallback for stray reload/back-button hits on /logout, avoids 405
Route::get('/logout', fn () => redirect()->route('auth.login.show'))->middleware('auth');

// FR-AUTH-01 — role-aware dashboard redirect so generic /dashboard link never 404s
Route::get('/dashboard', function () {
    /** @var User $user */
    $user = Auth::user();

    return $user->hasRole('super_admin')
        ? redirect()->route('admin.dashboard')
        : ($user->hasRole('guru')
            ? redirect()->route('guru.dashboard')
            : redirect()->route('siswa.dashboard'));
})->middleware('auth')->name('dashboard');

// FR-GR-05 / FR-SW-04 / §3.2 — shared download, authorized via MaterialPolicy (any role)
Route::get('/materials/{material}/download', MaterialDownloadController::class)
    ->middleware('auth')
    ->name('materials.download');

// ─── Super Admin ──────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:super_admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', fn () => view('admin.dashboard'))->name('dashboard');

    // FR-SA-02 / §8 — Users CRUD (no destroy = deactivation via status route)
    Route::resource('users', AdminUserController::class)->except(['destroy']);

    // FR-SA-04 — activity logs monitoring
    Route::get('monitoring', [AdminMonitoringController::class, 'index'])->name('monitoring');
    Route::patch('users/{user}/status', [AdminUserController::class, 'toggleStatus'])->name('users.status');
    Route::resource('classes', AdminSchoolClassController::class);
    Route::patch('classes/{class}/activate', [AdminSchoolClassController::class, 'activate'])->name('classes.activate');
    // FR-SA-05 / FR-GR-11 / FR-GR-12
    Route::get('recap', [RecapController::class, 'adminRecap'])->name('recap.index');
    Route::get('recap/export', [RecapController::class, 'exportAll'])->name('recap.export');
    Route::get('classes/{class}/recap', [RecapController::class, 'classRecap'])->name('classes.recap');
    Route::get('classes/{class}/recap/export', [RecapController::class, 'exportClass'])->name('classes.recap.export');
    Route::post('classes/{class}/grades/calculate', [GradeController::class, 'calculate'])->name('classes.grades.calculate');
    Route::resource('classes.grade-components', GradeComponentController::class)->only(['index', 'store', 'update', 'destroy']);

    // FR-SA-03 / FR-GR-04 / FR-GR-05 / BR-04 / ADMIN_CLASS_ACCESS_PLAN
    Route::resource('classes.materials', GuruMaterialController::class)->except(['show']);

    // FR-SA-03 / FR-GR-06 / FR-GR-07 / FR-GR-08
    Route::resource('classes.meetings', GuruMeetingController::class);
    Route::post('classes/{class}/meetings/{meeting}/materials', [GuruMeetingController::class, 'share'])
        ->name('classes.meetings.share');
    Route::get('classes/{class}/meetings/{meeting}/attendance', [GuruAttendanceController::class, 'edit'])
        ->name('classes.meetings.attendance.edit');
    Route::post('classes/{class}/meetings/{meeting}/attendance', [GuruAttendanceController::class, 'store'])
        ->name('classes.meetings.attendance.store');

    // FR-SA-03 / FR-GR-09
    Route::resource('classes.quizzes', GuruQuizController::class);
    Route::patch('classes/{class}/quizzes/{quiz}/publish', [GuruQuizController::class, 'publish'])->name('classes.quizzes.publish');
    Route::patch('classes/{class}/quizzes/{quiz}/unpublish', [GuruQuizController::class, 'unpublish'])->name('classes.quizzes.unpublish');
    Route::resource('classes.quizzes.questions', GuruQuizQuestionController::class)
        ->except(['index', 'show']);

    Route::post('classes/{class}/students', [GuruSchoolClassController::class, 'addStudent'])->name('classes.students.store');

    Route::get('classes/{class}/grade-components/{grade_component}/scores', [GradeComponentController::class, 'scores'])->name('classes.grade-components.scores');
    Route::post('classes/{class}/grade-components/{grade_component}/scores', [GradeComponentController::class, 'storeScores'])->name('classes.grade-components.scores.store');
});

// ─── Guru ─────────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:guru'])->prefix('guru')->name('guru.')->group(function () {
    Route::get('/dashboard', fn () => view('guru.dashboard'))->name('dashboard');
    Route::resource('classes', GuruSchoolClassController::class);
    Route::patch('classes/{class}/activate', [GuruSchoolClassController::class, 'activate'])->name('classes.activate');
    Route::post('classes/{class}/students', [GuruSchoolClassController::class, 'addStudent'])->name('classes.students.store');

    // FR-GR-04 / FR-GR-05 / BR-04
    Route::resource('classes.materials', GuruMaterialController::class)->except(['show']);

    // FR-GR-06 / FR-GR-08
    Route::resource('classes.meetings', GuruMeetingController::class);
    Route::post('classes/{class}/meetings/{meeting}/materials', [GuruMeetingController::class, 'share'])
        ->name('classes.meetings.share');

    // FR-GR-07
    Route::get('classes/{class}/meetings/{meeting}/attendance', [GuruAttendanceController::class, 'edit'])
        ->name('classes.meetings.attendance.edit');
    Route::post('classes/{class}/meetings/{meeting}/attendance', [GuruAttendanceController::class, 'store'])
        ->name('classes.meetings.attendance.store');

    // FR-GR-09
    Route::resource('classes.quizzes', GuruQuizController::class);
    Route::patch('classes/{class}/quizzes/{quiz}/publish', [GuruQuizController::class, 'publish'])->name('classes.quizzes.publish');
    Route::patch('classes/{class}/quizzes/{quiz}/unpublish', [GuruQuizController::class, 'unpublish'])->name('classes.quizzes.unpublish');
    Route::resource('classes.quizzes.questions', GuruQuizQuestionController::class)
        ->except(['index', 'show']);
    // FR-GR-10 / FR-GR-11 / FR-GR-12 / BR-03
    Route::resource('classes.grade-components', GradeComponentController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::get('classes/{class}/grade-components/{grade_component}/scores', [GradeComponentController::class, 'scores'])->name('classes.grade-components.scores');
    Route::post('classes/{class}/grade-components/{grade_component}/scores', [GradeComponentController::class, 'storeScores'])->name('classes.grade-components.scores.store');
    Route::post('classes/{class}/grades/calculate', [GradeController::class, 'calculate'])->name('classes.grades.calculate');
    Route::get('classes/{class}/recap', [RecapController::class, 'classRecap'])->name('classes.recap');
    Route::get('classes/{class}/recap/export', [RecapController::class, 'exportClass'])->name('classes.recap.export');
});

// ─── Siswa ────────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:siswa'])->prefix('siswa')->name('siswa.')->group(function () {
    Route::get('/dashboard', [RecapController::class, 'studentDashboard'])->name('dashboard');
    Route::post('/classes/join', [SiswaSchoolClassController::class, 'join'])->name('classes.join');
    Route::get('/classes', [SiswaSchoolClassController::class, 'index'])->name('classes.index');
    Route::get('/classes/{class}', [SiswaSchoolClassController::class, 'show'])->name('classes.show');

    // FR-SW-05
    Route::get('/quizzes/{quiz}', [SiswaQuizController::class, 'show'])->name('quizzes.show');
    Route::post('/quizzes/{quiz}/submit', [SiswaQuizController::class, 'submit'])->name('quizzes.submit');
    // FR-SW-06
    Route::get('/grades', [RecapController::class, 'studentGrades'])->name('grades.index');
});
