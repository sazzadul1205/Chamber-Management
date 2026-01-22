<?php

use App\Http\Controllers\ProcedureCatalogController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SystemSettingController;
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
    return view('backend.dashboard');
})->middleware(['auth', 'verified'])->name('backend.dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Roles Routes
    Route::prefix('roles')->name('backend.roles.')->group(function () {

        Route::get('/', [RoleController::class, 'index'])->name('index');
        Route::post('/', [RoleController::class, 'store'])->name('store');

        Route::put('/{role}', [RoleController::class, 'update'])->name('update');
        Route::delete('/{role}', [RoleController::class, 'destroy'])->name('destroy');

        // Soft delete
        Route::post('/{role}/restore', [RoleController::class, 'restore'])->name('restore');
        Route::delete('/{role}/force', [RoleController::class, 'forceDelete'])->name('force');
    });

    // Display the settings page (GET)
    Route::get('/settings', [SystemSettingController::class, 'index'])
        ->name('backend.system-settings.index') // <-- matches your view
        ->middleware('auth');

    // Handle form submission (POST)
    Route::post('/settings', [SystemSettingController::class, 'bulkUpdate'])
        ->name('backend.system-settings.bulk-update')
        ->middleware('auth');

    // Procedure Categories 
    Route::prefix('procedure-catalog')->name('backend.procedure-catalog.')->group(function () {
        Route::get('/', [ProcedureCatalogController::class, 'index'])->name('index');

        // Static routes first
        Route::get('/create', [ProcedureCatalogController::class, 'create'])->name('create');
        Route::post('/', [ProcedureCatalogController::class, 'store'])->name('store');
        Route::get('/import', [ProcedureCatalogController::class, 'import'])->name('import');
        Route::get('/autocomplete', [ProcedureCatalogController::class, 'autocomplete'])->name('autocomplete');

        // Dynamic routes last
        Route::get('/{procedureCatalog}', [ProcedureCatalogController::class, 'show'])->name('show');
        Route::get('/{procedureCatalog}/edit', [ProcedureCatalogController::class, 'edit'])->name('edit');
        Route::put('/{procedureCatalog}', [ProcedureCatalogController::class, 'update'])->name('update');
        Route::delete('/{procedureCatalog}', [ProcedureCatalogController::class, 'destroy'])->name('destroy');
    });
});

require __DIR__ . '/auth.php';
