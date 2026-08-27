<?php

use App\Http\Controllers\BookController;
use App\Http\Controllers\BookCopyController;
use App\Http\Controllers\BorrowingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MyBorrowingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RewardController;
use App\Http\Controllers\UserController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\ConditionReportController;


// ----- Public -----
Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
        ]);
        });
        
        // ----- Semua user yang login & sudah verifikasi email -----
        Route::middleware(['auth', 'verified'])->group(function () {
            Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
            
            Route::get('/catalog', [BookController::class, 'index'])->name('catalog.index');
            
            Route::get('/peminjaman-saya', [MyBorrowingController::class, 'index'])->name('my-borrowings.index');
            Route::get('/rewards', [RewardController::class, 'index'])->name('rewards.index');
            });
            
            // ----- Profile (gak wajib verified, biar tetep bisa dibuka) -----
            Route::middleware('auth')->group(function () {
                Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
                Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
                Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
                });
                
                // ----- Admin (OSIS) & Superadmin -----
                Route::middleware(['auth', 'verified', 'role:admin|superadmin'])->prefix('admin')->group(function () {
                    Route::get('/sirkulasi', [BorrowingController::class, 'index'])->name('sirkulasi.index');
                    Route::post('/sirkulasi/checkout', [BorrowingController::class, 'checkout'])->name('sirkulasi.checkout');
                    Route::post('/sirkulasi/checkin', [BorrowingController::class, 'checkin'])->name('sirkulasi.checkin');
                    Route::get('/sirkulasi/search-siswa', [BorrowingController::class, 'searchSiswa'])->name('sirkulasi.search-siswa');
                    Route::get('/sirkulasi/search-copy', [BorrowingController::class, 'searchCopy'])->name('sirkulasi.search-copy');
                    });
Route::post('/sirkulasi/condition-reports', [ConditionReportController::class, 'store'])->name('condition-reports.store');
                    
// ----- Superadmin saja -----
Route::middleware(['auth', 'verified', 'role:superadmin'])->group(function () {
    Route::get('/books/create', [BookController::class, 'create'])->name('books.create');
    Route::get('/books/{book}', [BookController::class, 'show'])->name('books.show');
    Route::post('/books', [BookController::class, 'store'])->name('books.store');
    Route::put('/books/{book}', [BookController::class, 'update'])->name('books.update');
    Route::get('/books/{book}/edit', [BookController::class, 'edit'])->name('books.edit');
    Route::delete('/books/{book}', [BookController::class, 'destroy'])->name('books.destroy');
    Route::post('/books/{book}/copies', [BookController::class, 'addCopy'])->name('books.add-copy');
    Route::put('/copies/{copy}', [BookCopyController::class, 'update'])->name('copies.update');
    Route::delete('/copies/{copy}', [BookCopyController::class, 'destroy'])->name('copies.destroy');

    Route::get('/condition-reports', [ConditionReportController::class, 'index'])->name('condition-reports.index');
Route::post('/condition-reports/{report}/approve', [ConditionReportController::class, 'approve'])->name('condition-reports.approve');
Route::post('/condition-reports/{report}/reject', [ConditionReportController::class, 'reject'])->name('condition-reports.reject');

    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::put('/users/{user}/role', [UserController::class, 'updateRole'])->name('users.update-role');
    Route::post('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');
});

require __DIR__.'/auth.php';