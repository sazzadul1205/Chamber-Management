<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\DentalChairController;
use App\Http\Controllers\DentalChartController;
use App\Http\Controllers\DiagnosisCodeController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\InventoryItemController;
use App\Http\Controllers\MedicineController;
use App\Http\Controllers\PatientFamilyController;
use App\Http\Controllers\ProcedureCatalogController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SystemSettingController;
use App\Http\Controllers\TreatmentController;
use App\Http\Controllers\TreatmentProcedureController;
use App\Http\Controllers\TreatmentSessionController;
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

    // Doctors Management
    Route::prefix('doctors')->name('backend.doctors.')->group(function () {

        // Base CRUD
        Route::get('/', [DoctorController::class, 'index'])->name('index');
        Route::get('/create', [DoctorController::class, 'create'])->name('create');
        Route::post('/', [DoctorController::class, 'store'])->name('store');

        // Utility
        Route::get('/generate-code', [DoctorController::class, 'generateCode'])->name('generate-code');
        Route::get('/available', [DoctorController::class, 'getAvailable'])->name('available');

        // Parameterized routes LAST
        Route::get('/{doctor}', [DoctorController::class, 'show'])->name('show');
        Route::get('/{doctor}/edit', [DoctorController::class, 'edit'])->name('edit');
        Route::put('/{doctor}', [DoctorController::class, 'update'])->name('update');
        Route::delete('/{doctor}', [DoctorController::class, 'destroy'])->name('destroy');
    });

    // Patient Families Management
    Route::prefix('patient-families')->name('backend.patient-families.')->group(function () {

        // Base CRUD
        Route::get('/', [PatientFamilyController::class, 'index'])->name('index');
        Route::get('/create', [PatientFamilyController::class, 'create'])->name('create');
        Route::post('/', [PatientFamilyController::class, 'store'])->name('store');

        // Static / utility routes first
        Route::get('/generate-code', [PatientFamilyController::class, 'generateCode'])->name('generate-code');

        // Family member operations
        Route::post('/{family}/members', [PatientFamilyController::class, 'addMember'])
            ->name('members.add');

        Route::delete('/{family}/members/{patient}', [PatientFamilyController::class, 'removeMember'])
            ->name('members.remove');

        Route::post('/{family}/set-head/{patient}', [PatientFamilyController::class, 'setHead'])
            ->name('set-head');

        // Parameterized CRUD routes LAST
        Route::get('/{family}', [PatientFamilyController::class, 'show'])->name('show');
        Route::get('/{family}/edit', [PatientFamilyController::class, 'edit'])->name('edit');
        Route::put('/{family}', [PatientFamilyController::class, 'update'])->name('update');
        Route::delete('/{family}', [PatientFamilyController::class, 'destroy'])->name('destroy');
    });

    // Dental Charts
    Route::prefix('dental-charts')->name('backend.dental-charts.')->group(function () {

        // Base CRUD
        Route::get('/', [DentalChartController::class, 'index'])->name('index');
        Route::get('/create', [DentalChartController::class, 'create'])->name('create');
        Route::post('/', [DentalChartController::class, 'store'])->name('store');

        // Utility / special routes (STATIC FIRST)
        Route::get('/patient/{patient}/chart', [DentalChartController::class, 'patientChart'])
            ->name('patient-chart');

        Route::post('/quick-add', [DentalChartController::class, 'quickAdd'])
            ->name('quick-add');

        Route::get('/api/patient/{patient}/chart-data', [DentalChartController::class, 'getPatientChartData'])
            ->name('patient-chart-data');

        // Parameterized CRUD routes LAST
        Route::get('/{dentalChart}', [DentalChartController::class, 'show'])->name('show');
        Route::get('/{dentalChart}/edit', [DentalChartController::class, 'edit'])->name('edit');
        Route::put('/{dentalChart}', [DentalChartController::class, 'update'])->name('update');
        Route::delete('/{dentalChart}', [DentalChartController::class, 'destroy'])->name('destroy');
    });

    // Appointments
    Route::prefix('appointments')->name('backend.appointments.')->group(function () {

        // Base CRUD
        Route::get('/', [AppointmentController::class, 'index'])->name('index');
        Route::get('/create', [AppointmentController::class, 'create'])->name('create');
        Route::post('/', [AppointmentController::class, 'store'])->name('store');

        // Views / dashboards (STATIC FIRST)
        Route::get('/calendar', [AppointmentController::class, 'calendar'])->name('calendar');
        Route::get('/today', [AppointmentController::class, 'today'])->name('today');

        // Utility / API-style routes
        Route::get('/api/available-slots', [AppointmentController::class, 'getAvailableSlots'])
            ->name('available-slots');

        // Status / workflow actions
        Route::post('/{appointment}/check-in', [AppointmentController::class, 'checkIn'])
            ->name('check-in');

        Route::post('/{appointment}/start', [AppointmentController::class, 'start'])
            ->name('start');

        Route::post('/{appointment}/complete', [AppointmentController::class, 'complete'])
            ->name('complete');

        Route::post('/{appointment}/cancel', [AppointmentController::class, 'cancel'])
            ->name('cancel');

        // Parameterized CRUD routes LAST
        Route::get('/{appointment}', [AppointmentController::class, 'show'])->name('show');
        Route::get('/{appointment}/edit', [AppointmentController::class, 'edit'])->name('edit');
        Route::put('/{appointment}', [AppointmentController::class, 'update'])->name('update');
        Route::delete('/{appointment}', [AppointmentController::class, 'destroy'])->name('destroy');
    });

    // Treatments
    Route::prefix('treatments')->name('backend.treatments.')->group(function () {

        // Base CRUD
        Route::get('/', [TreatmentController::class, 'index'])->name('index');
        Route::get('/create', [TreatmentController::class, 'create'])->name('create');
        Route::post('/', [TreatmentController::class, 'store'])->name('store');

        // Utility / quick actions (STATIC FIRST)
        Route::post('/quick-create', [TreatmentController::class, 'quickCreate'])
            ->name('quick-create');

        Route::get('/patient/{patientId}', [TreatmentController::class, 'patientTreatments'])
            ->name('patient-treatments');

        // Status / workflow actions
        Route::post('/{treatment}/start', [TreatmentController::class, 'start'])
            ->name('start');

        Route::post('/{treatment}/complete', [TreatmentController::class, 'complete'])
            ->name('complete');

        Route::post('/{treatment}/cancel', [TreatmentController::class, 'cancel'])
            ->name('cancel');

        Route::post('/{treatment}/hold', [TreatmentController::class, 'hold'])
            ->name('hold');

        Route::post('/{treatment}/resume', [TreatmentController::class, 'resume'])
            ->name('resume');

        Route::post('/{treatment}/add-session', [TreatmentController::class, 'addSession'])
            ->name('add-session');

        // Parameterized CRUD routes (ALWAYS LAST)
        Route::get('/{treatment}', [TreatmentController::class, 'show'])->name('show');
        Route::get('/{treatment}/edit', [TreatmentController::class, 'edit'])->name('edit');
        Route::put('/{treatment}', [TreatmentController::class, 'update'])->name('update');
        Route::delete('/{treatment}', [TreatmentController::class, 'destroy'])->name('destroy');
    });

    // Treatment Procedures
    Route::prefix('treatment-procedures')->name('backend.treatment-procedures.')->group(function () {

        // Base CRUD
        Route::get('/', [TreatmentProcedureController::class, 'index'])->name('index');
        Route::get('/create', [TreatmentProcedureController::class, 'create'])->name('create');
        Route::post('/', [TreatmentProcedureController::class, 'store'])->name('store');

        // Static / utility routes (STATIC FIRST)
        Route::get('/catalog/search', [TreatmentProcedureController::class, 'getCatalogProcedures'])
            ->name('catalog.search');

        // Create from a specific treatment
        Route::get('/create/{treatment}', [TreatmentProcedureController::class, 'create'])
            ->name('create-for-treatment');

        // Bulk actions
        Route::post('/{treatment}/bulk-add', [TreatmentProcedureController::class, 'bulkAdd'])
            ->name('bulk-add');

        // Treatment-wise listing
        Route::get('/treatment/{treatment}', [TreatmentProcedureController::class, 'treatmentProcedures'])
            ->name('by-treatment');

        // Workflow / status actions
        Route::post('/{treatmentProcedure}/start', [TreatmentProcedureController::class, 'start'])
            ->name('start');

        Route::post('/{treatmentProcedure}/complete', [TreatmentProcedureController::class, 'complete'])
            ->name('complete');

        Route::post('/{treatmentProcedure}/cancel', [TreatmentProcedureController::class, 'cancel'])
            ->name('cancel');

        // Parameterized CRUD routes (ALWAYS LAST)
        Route::get('/{treatmentProcedure}', [TreatmentProcedureController::class, 'show'])
            ->name('show');

        Route::get('/{treatmentProcedure}/edit', [TreatmentProcedureController::class, 'edit'])
            ->name('edit');

        Route::put('/{treatmentProcedure}', [TreatmentProcedureController::class, 'update'])
            ->name('update');

        Route::delete('/{treatmentProcedure}', [TreatmentProcedureController::class, 'destroy'])
            ->name('destroy');
    });

    // Treatment Sessions
    Route::prefix('treatment-sessions')->name('backend.treatment-sessions.')->group(function () {

        // Base CRUD
        Route::get('/', [TreatmentSessionController::class, 'index'])->name('index');
        Route::get('/create', [TreatmentSessionController::class, 'create'])->name('create');
        Route::post('/', [TreatmentSessionController::class, 'store'])->name('store');

        // Utility / static routes (STATIC FIRST)
        Route::get('/today', [TreatmentSessionController::class, 'today'])->name('today');

        Route::get('/treatment/{treatment}', [TreatmentSessionController::class, 'treatmentSessions'])
            ->name('by-treatment');

        Route::post('/quick-create/{treatment}', [TreatmentSessionController::class, 'quickCreate'])
            ->name('quick-create');

        // Workflow / status actions
        Route::post('/{treatmentSession}/start', [TreatmentSessionController::class, 'start'])->name('start');
        Route::post('/{treatmentSession}/complete', [TreatmentSessionController::class, 'complete'])->name('complete');
        Route::post('/{treatmentSession}/cancel', [TreatmentSessionController::class, 'cancel'])->name('cancel');
        Route::post('/{treatmentSession}/postpone', [TreatmentSessionController::class, 'postpone'])->name('postpone');
        Route::post('/{treatmentSession}/reschedule', [TreatmentSessionController::class, 'reschedule'])->name('reschedule');

        // Parameterized CRUD routes (ALWAYS LAST)
        Route::get('/{treatmentSession}', [TreatmentSessionController::class, 'show'])->name('show');
        Route::get('/{treatmentSession}/edit', [TreatmentSessionController::class, 'edit'])->name('edit');
        Route::put('/{treatmentSession}', [TreatmentSessionController::class, 'update'])->name('update');
        Route::delete('/{treatmentSession}', [TreatmentSessionController::class, 'destroy'])->name('destroy');
    });
});

require __DIR__ . '/auth.php';
