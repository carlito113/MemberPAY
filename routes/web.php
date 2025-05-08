<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SemesterController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\StudentAuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Group routes that require admin authentication
Route::middleware(['auth:admin'])->group(function () {
    // Admin Dashboard
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/admin/addpayment', [AdminController::class, 'showAddPaymentForm'])->name('admin.showaddpayment');
    Route::get('/admin/members', [AdminController::class, 'showMembers'])->name('admin.members');
    Route::patch('/admin/students/{id}/toggle-status', [AdminController::class, 'toggleStatus'])->name('admin.students.toggleStatus');

    // Super Admin Dashboard
    Route::get('/superadmin/dashboard', [AdminController::class, 'superAdminDashboard'])->name('superadmin.dashboard');
    // User Management for Super Admin
    Route::get('/superadmin/usermanagement', [AdminController::class, 'userManagementSuperAdmin'])->name('usermanagement.dashboard');
    Route::patch('/admins/{id}', [AdminController::class, 'updateAdminSuperadmin'])->name('admins.update');
    Route::get('/admin/payment', [AdminController::class, 'adminPayment'])->name('admin.addpayment');
    Route::post('/admin/addpayment', [AdminController::class, 'semStore'])->name('addpayment.semStore');
    Route::get('/admin/semesterrecord', [AdminController::class, 'semesterRecord'])->name('admin.semesterrecord');
    Route::get('/admin/set-semester/{id}', [AdminController::class, 'setSemester'])->name('admin.setSemester');

    // Member Management
    Route::post('/students/store', [StudentController::class, 'store'])->name('admin.students.store');
    Route::patch('/students/{student}/update', [StudentController::class, 'update'])->name('admin.students.update');
    Route::patch('/students/{student}/transfer', [StudentController::class, 'transfer'])->name('admin.students.transfer');

    // Payment Management
    Route::post('/admin/toggle-payment-status', [AdminController::class, 'togglePaymentStatus'])->name('admin.togglePaymentStatus');
    Route::post('/admin/update-payment-status', [AdminController::class, 'updatePaymentStatus'])->name('admin.updatePaymentStatus');
    Route::get('/admin/payment-history', [AdminController::class, 'paymentHistory'])->name('admin.paymenthistory');
    Route::get('/admin/payment-history-list', [AdminController::class, 'paymentHistoryList'])->name('admin.paymenthistorylist');
    Route::get('/admin/paymenthistorylist/pdf', [AdminController::class, 'downloadPaymentHistoryPDF'])->name('admin.paymenthistorylist.pdf');
    Route::post('/admin/remove-semester', [AdminController::class, 'removeSemester'])->name('admin.removeSemester');
});

Route::middleware(['auth:student'])->group(function () {
    Route::get('/student/dashboard', [StudentAuthController::class, 'dashboard'])->name('student.dashboard');
    Route::get('/student/organization', [StudentAuthController::class, 'viewCardOne'])->name('student.organizationcard');
    Route::get('/student/yearorganization', [StudentAuthController::class, 'viewCardTwo'])->name('student.yearorganizationcard');

    

    Route::get('/student/profile', [StudentAuthController::class, 'studentProfile'])->name('student.profile');
});
Route::get('/api/sections', [StudentController::class, 'getByYearAndOrg']);

