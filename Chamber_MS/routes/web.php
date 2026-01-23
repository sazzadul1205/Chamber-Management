<?php

use App\Http\Controllers\DentalChairController;
use App\Http\Controllers\DiagnosisCodeController;
use App\Http\Controllers\InventoryItemController;
use App\Http\Controllers\MedicineController;
use App\Http\Controllers\ProcedureCatalogController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SystemSettingController;
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

    // Diagnosis Codes
    Route::prefix('diagnosis-codes')->name('backend.diagnosis-codes.')->group(function () {
        Route::get('/', [DiagnosisCodeController::class, 'index'])->name('index');
        Route::get('/create', [DiagnosisCodeController::class, 'create'])->name('create');
        Route::post('/', [DiagnosisCodeController::class, 'store'])->name('store');

        // Static routes first
        Route::get('/export', [DiagnosisCodeController::class, 'export'])->name('export');
        Route::get('/autocomplete', [DiagnosisCodeController::class, 'autocomplete'])->name('autocomplete');
        Route::post('/quick-add', [DiagnosisCodeController::class, 'quickAdd'])->name('quick-add');

        // Parameterized routes last
        Route::get('/{diagnosisCode}/edit', [DiagnosisCodeController::class, 'edit'])->name('edit');
        Route::get('/{diagnosisCode}', [DiagnosisCodeController::class, 'show'])->name('show');
        Route::put('/{diagnosisCode}', [DiagnosisCodeController::class, 'update'])->name('update');
        Route::delete('/{diagnosisCode}', [DiagnosisCodeController::class, 'destroy'])->name('destroy');
    });

    // Inventory Items
    Route::prefix('inventory-items')->name('backend.inventory-items.')->group(function () {

        // CRUD base routes
        Route::get('/', [InventoryItemController::class, 'index'])->name('index');
        Route::get('/create', [InventoryItemController::class, 'create'])->name('create');
        Route::post('/', [InventoryItemController::class, 'store'])->name('store');

        // Static / utility routes first
        Route::get('/export', [InventoryItemController::class, 'export'])->name('export');
        Route::get('/autocomplete', [InventoryItemController::class, 'autocomplete'])->name('autocomplete');
        Route::get('/generate-code', [InventoryItemController::class, 'generateCode'])->name('generate-code');

        // Parameterized routes last
        Route::get('/{inventoryItem}/edit', [InventoryItemController::class, 'edit'])->name('edit');
        Route::get('/{inventoryItem}', [InventoryItemController::class, 'show'])->name('show');
        Route::put('/{inventoryItem}', [InventoryItemController::class, 'update'])->name('update');
        Route::delete('/{inventoryItem}', [InventoryItemController::class, 'destroy'])->name('destroy');
    });

    // Medicines
    Route::prefix('medicines')->name('backend.medicines.')->group(function () {

        // CRUD base routes
        Route::get('/', [MedicineController::class, 'index'])->name('index');
        Route::get('/create', [MedicineController::class, 'create'])->name('create');
        Route::post('/', [MedicineController::class, 'store'])->name('store');

        // Static / utility routes first
        Route::get('/export', [MedicineController::class, 'export'])->name('export');
        Route::get('/import', [MedicineController::class, 'import'])->name('import');
        Route::post('/process-import', [MedicineController::class, 'processImport'])->name('process-import');
        Route::get('/autocomplete', [MedicineController::class, 'autocomplete'])->name('autocomplete');
        Route::get('/generate-code', [MedicineController::class, 'generateCode'])->name('generate-code');

        // Parameterized routes last
        Route::get('/{medicine}/edit', [MedicineController::class, 'edit'])->name('edit');
        Route::get('/{medicine}', [MedicineController::class, 'show'])->name('show');
        Route::put('/{medicine}', [MedicineController::class, 'update'])->name('update');
        Route::delete('/{medicine}', [MedicineController::class, 'destroy'])->name('destroy');
    });

    // Dental Chairs
    Route::prefix('dental-chairs')->name('backend.dental-chairs.')->group(function () {

        // CRUD base routes
        Route::get('/', [DentalChairController::class, 'index'])->name('index');
        Route::get('/create', [DentalChairController::class, 'create'])->name('create');
        Route::post('/', [DentalChairController::class, 'store'])->name('store');

        // Static / utility routes first
        Route::get('/dashboard', [DentalChairController::class, 'dashboard'])->name('dashboard');
        Route::get('/schedule', [DentalChairController::class, 'schedule'])->name('schedule');
        Route::get('/generate-code', [DentalChairController::class, 'generateCode'])->name('generate-code');

        // API / status routes
        Route::get('/api/available-chairs', [DentalChairController::class, 'getAvailableChairs'])->name('available-chairs');
        Route::post('/{dentalChair}/update-status', [DentalChairController::class, 'updateStatus'])->name('update-status');
        Route::post('/{dentalChair}/quick-status-change', [DentalChairController::class, 'quickStatusChange'])->name('quick-status-change');

        // Parameterized CRUD routes last
        Route::get('/{dentalChair}', [DentalChairController::class, 'show'])->name('show');
        Route::get('/{dentalChair}/edit', [DentalChairController::class, 'edit'])->name('edit');
        Route::put('/{dentalChair}', [DentalChairController::class, 'update'])->name('update');
        Route::delete('/{dentalChair}', [DentalChairController::class, 'destroy'])->name('destroy');
    });

    // Users Management
    Route::prefix('users')->name('backend.user.')->group(function () {

        // Base CRUD
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::post('/', [UserController::class, 'store'])->name('store');

        // Utility
        Route::post('/{user}/reset-password', [UserController::class, 'resetPassword'])
            ->name('reset-password');

        // Soft delete handling
        Route::post('/{user}/restore', [UserController::class, 'restore'])
            ->name('restore');
        Route::delete('/{user}/force', [UserController::class, 'forceDelete'])
            ->name('force');

        // Parameterized routes LAST
        Route::get('/{user}', [UserController::class, 'show'])->name('show');
        Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
        Route::put('/{user}', [UserController::class, 'update'])->name('update');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
    });
});

require __DIR__ . '/auth.php';
