<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CalculatorController;
use App\Http\Controllers\TeamMemberController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ConveyanceController;
use App\Http\Controllers\TeamController;

// ── Public Routes ────────────────────────────────────
Route::get('/', fn() => redirect()->route('login'));
Route::get('/register',  [RegisterController::class, 'showForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);
Route::get('/login',     [LoginController::class, 'showForm'])->name('login');
Route::post('/login',    [LoginController::class, 'login']);
Route::post('/logout',   [LoginController::class, 'logout'])->name('logout');

// Forgot + Reset Password
Route::get('/forgot-password',        [ForgotPasswordController::class, 'showForm'])->name('password.request');
Route::post('/forgot-password',       [ForgotPasswordController::class, 'sendResetLink'])->name('password.email');
Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showForm'])->name('password.reset');
Route::post('/reset-password',        [ResetPasswordController::class, 'reset'])->name('password.update');

// ── Protected Routes ─────────────────────────────────
Route::middleware(['auth'])->group(function () {

    // ── Dashboard ────────────────────────────────────
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::prefix('dashboard')->name('dashboard.')->group(function () {
        Route::get('/stats',               [DashboardController::class, 'stats'])->name('stats');
        Route::get('/targets',             [DashboardController::class, 'targets'])->name('targets');
        Route::get('/aum-trend',           [DashboardController::class, 'aumTrend'])->name('aum-trend');
        Route::get('/aum-table',  [DashboardController::class, 'aumTable'])->name('aum-table');
        Route::get('/aum-rm-list',[DashboardController::class, 'aumRmList'])->name('aum-rm-list');
        Route::get('/recent-activities',   [DashboardController::class, 'recentActivities'])->name('recent-activities');
        Route::get('/pending-conveyances', [DashboardController::class, 'pendingConveyances'])->name('pending-conveyances');
        Route::get('/today-birthdays',     [DashboardController::class, 'todayBirthdays'])->name('today-birthdays');
        Route::get('/today-meetings',      [DashboardController::class, 'todayMeetings'])->name('today-meetings');
    });

    // ── Profile ──────────────────────────────────────
    Route::get('/profile',           [ProfileController::class, 'index'])->name('profile');
    Route::post('/profile/update',   [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/password', [ProfileController::class, 'changePassword'])->name('profile.password');

    // ── Calculators ───────────────────────────────────
    Route::middleware(['role:super_admin|team_member'])->group(function () {
        Route::get('/calculators', [CalculatorController::class, 'index'])->name('calculators');
    });

    // ── Super Admin Only ──────────────────────────────
    Route::middleware(['role:super_admin'])->group(function () {

        // Team Members
        Route::get('/team-members',                       [TeamMemberController::class, 'index'])->name('team-members.index');
        Route::get('/team-members/list',                  [TeamMemberController::class, 'list'])->name('team-members.list');
        Route::get('/team-members/generate-code',         [TeamMemberController::class, 'generateCode'])->name('team-members.generate-code');
        Route::get('/team-members/sample-target',         [TeamMemberController::class, 'downloadSampleTarget'])->name('team-members.sample-target');
        Route::get('/team-members/export/excel',          [TeamMemberController::class, 'exportExcel'])->name('team-members.export-excel');
        Route::post('/team-members/upload-target',        [TeamMemberController::class, 'uploadTarget'])->name('team-members.upload-target');
        Route::post('/team-members',                      [TeamMemberController::class, 'store'])->name('team-members.store');
        Route::get('/team-members/{teamMember}',          [TeamMemberController::class, 'show'])->name('team-members.show');
        Route::put('/team-members/{teamMember}',          [TeamMemberController::class, 'update'])->name('team-members.update');
        Route::patch('/team-members/{teamMember}/status', [TeamMemberController::class, 'toggleStatus'])->name('team-members.toggle-status');
        Route::delete('/team-members/{teamMember}',       [TeamMemberController::class, 'destroy'])->name('team-members.destroy');
        Route::post('/team-members/{teamMember}/target',  [TeamMemberController::class, 'setTarget'])->name('team-members.set-target');

        // Teams — Static FIRST
        Route::get('/teams',                              [TeamController::class, 'index'])->name('teams.index');
        Route::get('/teams/list',                         [TeamController::class, 'list'])->name('teams.list');
        Route::get('/teams/generate-code',                [TeamController::class, 'generateCode'])->name('teams.generate-code');
        Route::post('/teams',                             [TeamController::class, 'store'])->name('teams.store');
        // Teams — Dynamic AFTER
        Route::get('/teams/{team}/members',               [TeamController::class, 'members'])->name('teams.members');
        Route::get('/teams/{team}/members/list',          [TeamController::class, 'membersList'])->name('teams.members-list');
        Route::post('/teams/{team}/members/assign',       [TeamController::class, 'assignMember'])->name('teams.assign-member');
        Route::delete('/teams/{team}/members/{user}',     [TeamController::class, 'removeMember'])->name('teams.remove-member');
        Route::post('/teams/{team}/transfer-clients',     [TeamController::class, 'transferClients'])->name('teams.transfer-clients');
        Route::get('/teams/{team}',                       [TeamController::class, 'show'])->name('teams.show');
        Route::put('/teams/{team}',                       [TeamController::class, 'update'])->name('teams.update');
        Route::patch('/teams/{team}/status',              [TeamController::class, 'toggleStatus'])->name('teams.toggle-status');
        Route::delete('/teams/{team}',                    [TeamController::class, 'destroy'])->name('teams.destroy');

        // Conveyance approve/reject
        Route::post('/conveyance/{conveyance}/approve',   [ConveyanceController::class, 'approve'])->name('conveyance.approve');
        Route::post('/conveyance/{conveyance}/reject',    [ConveyanceController::class, 'reject'])->name('conveyance.reject');
    });

    // ── Customers ─────────────────────────────────────
    Route::get('/customers',                      [CustomerController::class, 'index'])->name('customers.index');
    Route::get('/customers/list',                 [CustomerController::class, 'list'])->name('customers.list');
    Route::get('/customers/export/excel',         [CustomerController::class, 'exportExcel'])->name('customers.export-excel');
    Route::post('/customers',                     [CustomerController::class, 'store'])->name('customers.store');
    Route::get('/customers/{customer}/profile',   [CustomerController::class, 'profile'])->name('customers.profile');
    Route::get('/customers/{customer}',           [CustomerController::class, 'show'])->name('customers.show');
    Route::put('/customers/{customer}',           [CustomerController::class, 'update'])->name('customers.update');
    Route::patch('/customers/{customer}/status',  [CustomerController::class, 'toggleStatus'])->name('customers.toggle-status');
    Route::delete('/customers/{customer}',        [CustomerController::class, 'destroy'])->name('customers.destroy');

    // ── Conveyance ────────────────────────────────────
    Route::get('/conveyance',                     [ConveyanceController::class, 'index'])->name('conveyance.index');
    Route::get('/conveyance/list',                [ConveyanceController::class, 'list'])->name('conveyance.list');
    Route::get('/conveyance/stats',               [ConveyanceController::class, 'stats'])->name('conveyance.stats');
    Route::get('/conveyance/export/excel',        [ConveyanceController::class, 'exportExcel'])->name('conveyance.export-excel');
    Route::post('/conveyance',                    [ConveyanceController::class, 'store'])->name('conveyance.store');
    Route::delete('/conveyance/{conveyance}',     [ConveyanceController::class, 'destroy'])->name('conveyance.destroy');

    // ── Customer Portal ───────────────────────────────
    Route::get('/customer/dashboard', fn() => view('dashboard.index'))->name('customer.dashboard');

});