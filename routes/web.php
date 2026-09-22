<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Dashboard
Route::get('dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

// Students
Route::controller(StudentController::class)->group(function() {
    Route::group(['prefix' => 'students'], function(){
        Route::get('/', 'index')->name('students.index');
        Route::get('/create', 'create')->name('students.create');
        Route::post('/', 'store')->name('students.store');
        Route::get('/edit/{id}', 'edit')->name('students.edit');
        Route::put('/{id}', 'update')->name('students.update');
        Route::delete('/{id}', 'destroy')->name('students.destroy');
        Route::get('/{id}', 'show')->name('students.show');
    });
});

// Teachers
Route::controller(TeacherController::class)->group(function() {
    Route::group(['prefix' => 'teachers'], function(){
        Route::get('/',  'index')->name('teachers.index');
        Route::get('/create',  'create')->name('teachers.create');
        Route::post('/',  'store')->name('teachers.store');
        Route::get('/edit/{id}', 'edit')->name('teachers.edit');
        Route::put('/{id}',  'update')->name('teachers.update');
        Route::delete('/{id}',  'destroy')->name('teachers.destroy');
        Route::get('/{id}', 'show')->name('teachers.show');
    });
});

Route::fallback(function() {
    return Inertia::render('Errors/NotFound');
});

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

// Route::get('/dashboard', function () {
//     return Inertia::render('Dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';