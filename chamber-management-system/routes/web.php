<?php

use App\Http\Controllers\Backend\DashboardController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::middleware('auth')->group(function () {

        Route::get('/backend/dashboard', [DashboardController::class, 'index'])
            ->name('backend.dashboard');

        Route::prefix('backend')->name('backend.')->group(function () {

            // Roles
            Route::get('/roles', [RoleController::class, 'index'])
                ->name('roles.index');

            // User Status Toggle
            Route::patch('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])
                ->name('users.toggle-status');

            // Users (FULL RESOURCE)
            Route::resource('users', UserController::class);

            // Patients (FULL RESOURCE)
            Route::resource('patients', PatientController::class);
        });
    });
});

require __DIR__ . '/auth.php';
