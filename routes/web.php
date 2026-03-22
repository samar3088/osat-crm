<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\CalculatorController;
use App\Http\Controllers\TeamMemberController;

// ── Public Routes ────────────────────────────────────
Route::get('/', fn() => redirect()->route('login'));

Route::get('/register',  [RegisterController::class, 'showForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);
Route::get('/login',     [LoginController::class, 'showForm'])->name('login');
Route::post('/login',    [LoginController::class, 'login']);
Route::post('/logout',   [LoginController::class, 'logout'])->name('logout');
Route::get('/forgot-password', fn() => view('auth.login'))->name('password.request');

// Forgot Password
Route::get('/forgot-password',        [ForgotPasswordController::class, 'showForm'])->name('password.request');
Route::post('/forgot-password',       [ForgotPasswordController::class, 'sendResetLink'])->name('password.email');

// Reset Password
Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showForm'])->name('password.reset');
Route::post('/reset-password',        [ResetPasswordController::class, 'reset'])->name('password.update');

// ── Protected Routes ─────────────────────────────────
Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Dashboard AJAX endpoints
    Route::prefix('dashboard')->name('dashboard.')->group(function () {
        Route::get('/stats',               [DashboardController::class, 'stats'])->name('stats');
        Route::get('/targets',             [DashboardController::class, 'targets'])->name('targets');
        Route::get('/aum-trend',           [DashboardController::class, 'aumTrend'])->name('aum-trend');
        Route::get('/recent-activities',   [DashboardController::class, 'recentActivities'])->name('recent-activities');
        Route::get('/pending-conveyances', [DashboardController::class, 'pendingConveyances'])->name('pending-conveyances');
        Route::get('/today-birthdays',     [DashboardController::class, 'todayBirthdays'])->name('today-birthdays');
        Route::get('/today-meetings',      [DashboardController::class, 'todayMeetings'])->name('today-meetings');
    });

    // ── Profile ──────────────────────────────────────
    Route::get('/profile',           [ProfileController::class, 'index'])->name('profile');
    Route::post('/profile/update',   [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/password', [ProfileController::class, 'changePassword'])->name('profile.password');

    // Calculators
    Route::middleware(['role:super_admin|team_member'])->group(function () {
        Route::get('/calculators', [CalculatorController::class, 'index'])->name('calculators');
    });

    // Team Members — Super Admin only
    Route::middleware(['role:super_admin'])->group(function () {
        Route::get('/team-members',                         [TeamMemberController::class, 'index'])->name('team-members.index');
        Route::get('/team-members/list',                    [TeamMemberController::class, 'list'])->name('team-members.list');
        Route::post('/team-members',                        [TeamMemberController::class, 'store'])->name('team-members.store');
        Route::get('/team-members/{teamMember}',            [TeamMemberController::class, 'show'])->name('team-members.show');
        Route::put('/team-members/{teamMember}',            [TeamMemberController::class, 'update'])->name('team-members.update');
        Route::patch('/team-members/{teamMember}/status',   [TeamMemberController::class, 'toggleStatus'])->name('team-members.toggle-status');
        Route::delete('/team-members/{teamMember}',         [TeamMemberController::class, 'destroy'])->name('team-members.destroy');
        Route::post('/team-members/{teamMember}/target',    [TeamMemberController::class, 'setTarget'])->name('team-members.set-target');

        Route::get('/team-members/export/excel',   [TeamMemberController::class, 'exportExcel'])->name('team-members.export-excel');
        Route::get('/team-members/export/pdf',     [TeamMemberController::class, 'exportPdf'])->name('team-members.export-pdf');
        Route::get('/team-members/sample-target',  [TeamMemberController::class, 'downloadSampleTarget'])->name('team-members.sample-target');
        Route::post('/team-members/upload-target', [TeamMemberController::class, 'uploadTarget'])->name('team-members.upload-target');
    });

    // Customer dashboard
    Route::get('/customer/dashboard', fn() => view('dashboard.index'))->name('customer.dashboard');

});