<?php

use App\Http\Controllers\ClassesController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\UserController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware(['auth'])->group(function() {
    // Dashboard
    Route::get('dashboard', [DashboardController::class, 'index'])->middleware(['verified'])->name('dashboard');

    // Students
    Route::controller(StudentController::class)->group(function() {
        Route::group(['prefix' => 'students'], function(){
            Route::get('/', 'index')->name('students.index');
            Route::get('/create', 'create')->name('students.create');
            Route::post('/', 'store')->name('students.store');
            Route::get('/export', 'export')->name('students.export');
            Route::post('/import', 'import')->name('students.import');
            Route::get('/{id}/report-pdf', 'studentReport')->name('students.report.pdf');
            Route::get('/{id}/email-report', 'emailReport')->name('students.email.report');
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

    // Classes
    Route::controller(ClassesController::class)->group(function () {
        Route::group(['prefix' => 'classes'], function(){
            Route::get('/', 'index')->name('classes.index');
            Route::get('/create', 'create')->name('classes.create');
            Route::post('/', 'store')->name('classes.store');
            Route::get('/edit/{id}', 'edit')->name('classes.edit');
            Route::put('/{id}', 'update')->name('classes.update');
            Route::delete('/{id}', 'destroy')->name('classes.destroy');
            Route::get('/{id}', 'show')->name('classes.show');
        });
    });

    // Users
    Route::controller(UserController::class)->group(function () {
        Route::group(['prefix' => 'users'], function(){
            Route::get('/', 'index')->name('users.index');
            Route::get('/create', 'create')->name('users.create');
            Route::post('/', 'store')->name('users.store');
            Route::get('/edit/{id}', 'edit')->name('users.edit');
            Route::put('/{id}', 'update')->name('users.update');
            Route::delete('/{id}', 'destroy')->name('users.destroy');
            Route::get('/{id}', 'show')->name('users.show');
        });
    });

    // Roles and Permissions
    Route::prefix('roles')->group(function() {
        Route::get('/', [RolePermissionController::class, 'index'])->name('roles.index');
        Route::get('/create', [RolePermissionController::class, 'create'])->name('roles.create');
        Route::post('/roles', [RolePermissionController::class, 'store'])->name('roles.store');
        Route::get('add-permission-to-role/{id}', [RolePermissionController::class, 'addPermissionToRole']);
        Route::post('assign-permissions-to-role/{id}', [RolePermissionController::class, 'assignPermissions']);
        Route::get('add-users-to-role/{id}', [RolePermissionController::class, 'addUsersToRole']);
        Route::post('assign-users-to-role/{id}', [RolePermissionController::class, 'assignUsersToRole']);
    });

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::redirect('/', '/login');

// Route::get('/', function () {
//     return Inertia::render('Welcome', [
//         'canLogin' => Route::has('login'),
//         'canRegister' => Route::has('register'),
//         'laravelVersion' => Application::VERSION,
//         'phpVersion' => PHP_VERSION,
//     ]);
// });

require __DIR__.'/auth.php';

Route::fallback(function() {
    return Inertia::render('Errors/NotFound');
});