<?php

use App\Http\Controllers\{
    AppointmentController,
    DentalChairController,
    DentalChartController,
    DiagnosisCodeController,
    DoctorController,
    InventoryItemController,
    InventoryStockController,
    InventoryTransactionController,
    InventoryUsageController,
    InvoiceController,
    InvoiceItemController,
    MedicalFileController,
    MedicineController,
    PatientController,
    PatientFamilyController,
    PaymentAllocationController,
    PaymentController,
    PaymentInstallmentController,
    PrescriptionController,
    ProcedureCatalogController,
    ProfileController,
    ReceiptController,
    RoleController,
    SystemSettingController,
    TreatmentController,
    TreatmentProcedureController,
    TreatmentSessionController,
    UserController
};

use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', fn() => view('welcome'));
Route::get('/dashboard', fn() => view('backend.dashboard'))
    ->middleware(['auth', 'verified'])
    ->name('backend.dashboard');

Route::middleware(['auth'])->group(function () {

    // -----------------------------
    // Profile
    // -----------------------------
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
    });

    // -----------------------------
    // Roles
    // -----------------------------
    Route::resource('roles', RoleController::class)
        ->except(['show', 'create', 'edit'])
        ->names('backend.roles');

    Route::post('roles/{role}/restore', [RoleController::class, 'restore'])->name('backend.roles.restore');
    Route::delete('roles/{role}/force', [RoleController::class, 'forceDelete'])->name('backend.roles.force');

    // -----------------------------
    // System Settings
    // -----------------------------
    Route::get('/settings', [SystemSettingController::class, 'index'])->name('backend.system-settings.index');
    Route::post('/settings', [SystemSettingController::class, 'bulkUpdate'])->name('backend.system-settings.bulk-update');

    // -----------------------------
    // Procedure Catalog
    // -----------------------------
    Route::resource('procedure-catalog', ProcedureCatalogController::class)
        ->names('backend.procedure-catalog');

    Route::get('procedure-catalog/import', [ProcedureCatalogController::class, 'import'])->name('backend.procedure-catalog.import');
    Route::get('procedure-catalog/autocomplete', [ProcedureCatalogController::class, 'autocomplete'])->name('backend.procedure-catalog.autocomplete');
    // routes/web.php
    Route::get('backend/treatment-procedures/catalog-search', [TreatmentProcedureController::class, 'catalogSearch'])
        ->name('backend.treatment-procedures.get-catalog-procedures');


    // -----------------------------
    // Diagnosis Codes
    // -----------------------------
    Route::resource('diagnosis-codes', DiagnosisCodeController::class)
        ->names('backend.diagnosis-codes');

    Route::get('diagnosis-codes/export', [DiagnosisCodeController::class, 'export'])->name('backend.diagnosis-codes.export');
    Route::get('diagnosis-codes/autocomplete', [DiagnosisCodeController::class, 'autocomplete'])->name('backend.diagnosis-codes.autocomplete');
    Route::post('diagnosis-codes/quick-add', [DiagnosisCodeController::class, 'quickAdd'])->name('backend.diagnosis-codes.quick-add');

    // -----------------------------
    // Inventory Items
    // -----------------------------
    Route::resource('inventory-items', InventoryItemController::class)->names('backend.inventory-items');
    Route::get('inventory-items/export', [InventoryItemController::class, 'export'])->name('backend.inventory-items.export');
    Route::get('inventory-items/autocomplete', [InventoryItemController::class, 'autocomplete'])->name('backend.inventory-items.autocomplete');
    Route::get('inventory-items/generate-code', [InventoryItemController::class, 'generateCode'])->name('backend.inventory-items.generate-code');

    // -----------------------------
    // Medicines
    // -----------------------------
    Route::resource('medicines', MedicineController::class)->names('backend.medicines');
    Route::get('medicines/export', [MedicineController::class, 'export'])->name('backend.medicines.export');
    Route::get('medicines/import', [MedicineController::class, 'import'])->name('backend.medicines.import');
    Route::post('medicines/process-import', [MedicineController::class, 'processImport'])->name('backend.medicines.process-import');
    Route::get('medicines/autocomplete', [MedicineController::class, 'autocomplete'])->name('backend.medicines.autocomplete');
    Route::get('medicines/generate-code', [MedicineController::class, 'generateCode'])->name('backend.medicines.generate-code');

    // -----------------------------
    // Dental Chairs
    // -----------------------------
    Route::resource('dental-chairs', DentalChairController::class)->names('backend.dental-chairs');
    Route::get('dental-chairs/dashboard', [DentalChairController::class, 'dashboard'])->name('backend.dental-chairs.dashboard');
    Route::get('dental-chairs/schedule', [DentalChairController::class, 'schedule'])->name('backend.dental-chairs.schedule');
    Route::get('dental-chairs/generate-code', [DentalChairController::class, 'generateCode'])->name('backend.dental-chairs.generate-code');
    Route::get('dental-chairs/api/available-chairs', [DentalChairController::class, 'getAvailableChairs'])->name('backend.dental-chairs.available-chairs');
    Route::post('dental-chairs/{dentalChair}/update-status', [DentalChairController::class, 'updateStatus'])->name('backend.dental-chairs.update-status');
    Route::post('dental-chairs/{dentalChair}/quick-status-change', [DentalChairController::class, 'quickStatusChange'])->name('backend.dental-chairs.quick-status-change');

    // -----------------------------
    // Users
    // -----------------------------
    Route::resource('users', UserController::class)->names('backend.user');
    Route::post('users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('backend.user.reset-password');
    Route::post('users/{user}/restore', [UserController::class, 'restore'])->name('backend.user.restore');
    Route::delete('users/{user}/force', [UserController::class, 'forceDelete'])->name('backend.user.force');

    // -----------------------------
    // Doctors
    // -----------------------------
    Route::resource('doctors', DoctorController::class)->names('backend.doctors');
    Route::get('doctors/generate-code', [DoctorController::class, 'generateCode'])->name('backend.doctors.generate-code');
    Route::get('doctors/available', [DoctorController::class, 'getAvailable'])->name('backend.doctors.available');

    // -----------------------------
    // Patients & Families
    // -----------------------------
    Route::resource('backend/patients', PatientController::class)->names('backend.patients');
    Route::post('backend/patients/quick-add', [PatientController::class, 'quickAdd'])->name('backend.patients.quick_add');
    Route::get('backend/patients/search/ajax', [PatientController::class, 'search'])->name('backend.patients.search');
    Route::get('backend/patients/{patient}/medical-history', [PatientController::class, 'medicalHistory'])->name('backend.patients.medical_history');

    // Patient Families
    Route::resource('patient-families', PatientFamilyController::class)->names('backend.patient-families');
    Route::get('patient-families/generate-code', [PatientFamilyController::class, 'generateCode'])->name('backend.patient-families.generate-code');
    Route::post('patient-families/{patientFamily}/members', [PatientFamilyController::class, 'addMember'])->name('backend.patient-families.members.add');
    Route::delete('patient-families/{patientFamily}/members/{patient}', [PatientFamilyController::class, 'removeMember'])->name('backend.patient-families.members.remove');
    Route::post('patient-families/{patientFamily}/set-head/{patient}', [PatientFamilyController::class, 'setHead'])->name('backend.patient-families.set-head');

    // -----------------------------
    // Dental Charts
    // -----------------------------
    Route::resource('dental-charts', DentalChartController::class)->names('backend.dental-charts');
    Route::get('dental-charts/patient/{patient}/chart', [DentalChartController::class, 'patientChart'])->name('backend.dental-charts.patient-chart');
    Route::post('dental-charts/quick-add', [DentalChartController::class, 'quickAdd'])->name('backend.dental-charts.quick-add');
    Route::get('dental-charts/api/patient/{patient}/chart-data', [DentalChartController::class, 'getPatientChartData'])->name('backend.dental-charts.patient-chart-data');

    // -----------------------------
    // Appointments
    // -----------------------------
    Route::get('appointments/calendar', [AppointmentController::class, 'calendar'])->name('backend.appointments.calendar');
    Route::get('appointments/today', [AppointmentController::class, 'today'])->name('backend.appointments.today');
    Route::get('appointments/api/available-slots', [AppointmentController::class, 'getAvailableSlots'])->name('backend.appointments.available-slots');
    Route::post('appointments/{appointment}/check-in', [AppointmentController::class, 'checkIn'])->name('backend.appointments.check-in');
    Route::post('appointments/{appointment}/start', [AppointmentController::class, 'start'])->name('backend.appointments.start');
    Route::post('appointments/{appointment}/complete', [AppointmentController::class, 'complete'])->name('backend.appointments.complete');
    Route::post('appointments/{appointment}/cancel', [AppointmentController::class, 'cancel'])->name('backend.appointments.cancel');
    Route::put('appointments/{appointment}/reschedule', [AppointmentController::class, 'reschedule'])->name('backend.appointments.reschedule');
    Route::post('appointments/{appointment}/no-show', [AppointmentController::class, 'noShow'])->name('backend.appointments.no-show');
    Route::get('appointments/{appointment}/details', [AppointmentController::class, 'getDetails'])->name('backend.appointments.details');

    // Queue Display (TV)
    Route::get('appointments/queue', [AppointmentController::class, 'queue'])->name('backend.appointments.queue');

    // Walk-in Appointment
    Route::get('appointments/walk-in', [AppointmentController::class, 'walkInForm'])->name('backend.appointments.walk-in');
    Route::post('appointments/walk-in', [AppointmentController::class, 'walkInStore'])->name('backend.appointments.walk-in.store');
    Route::resource('appointments', AppointmentController::class)->names('backend.appointments');

    // -----------------------------
    // Treatments & Procedures & Sessions
    // -----------------------------

    // Resource routes first
    Route::resource('treatments', TreatmentController::class)->names('backend.treatments');

    // Specific custom routes
    Route::post('treatments/quick-create', [TreatmentController::class, 'quickCreate'])->name('backend.treatments.quick-create');
    Route::get('treatments/patient/{patientId}', [TreatmentController::class, 'patientTreatments'])->name('backend.treatments.patient-treatments');

    // Actions on a single treatment
    Route::prefix('treatments/{treatment}')->name('backend.treatments.')->group(function () {
        Route::post('start', [TreatmentController::class, 'start'])->name('start');
        Route::post('complete', [TreatmentController::class, 'complete'])->name('complete');
        Route::post('cancel', [TreatmentController::class, 'cancel'])->name('cancel');
        Route::post('hold', [TreatmentController::class, 'hold'])->name('hold');
        Route::post('resume', [TreatmentController::class, 'resume'])->name('resume');
        Route::post('add-session', [TreatmentController::class, 'addSession'])->name('add-session');
    });

    Route::resource('treatment-procedures', TreatmentProcedureController::class)->names('backend.treatment-procedures');
    Route::get('treatment-procedures/catalog/search', [TreatmentProcedureController::class, 'getCatalogProcedures'])->name('backend.treatment-procedures.catalog.search');
    Route::get('treatment-procedures/create/{treatment}', [TreatmentProcedureController::class, 'create'])->name('backend.treatment-procedures.create-for-treatment');
    Route::post('treatment-procedures/{treatment}/bulk-add', [TreatmentProcedureController::class, 'bulkAdd'])->name('backend.treatment-procedures.bulk-add');
    Route::get('treatment-procedures/treatment/{treatment}', [TreatmentProcedureController::class, 'treatmentProcedures'])->name('backend.treatment-procedures.by-treatment');
    Route::post('treatment-procedures/{procedure}/start', [TreatmentProcedureController::class, 'start'])->name('backend.treatment-procedures.start');
    Route::post('treatment-procedures/{procedure}/complete', [TreatmentProcedureController::class, 'complete'])->name('backend.treatment-procedures.complete');
    Route::post('treatment-procedures/{procedure}/cancel', [TreatmentProcedureController::class, 'cancel'])->name('backend.treatment-procedures.cancel');

    Route::resource('treatment-sessions', TreatmentSessionController::class)->names('backend.treatment-sessions');
    Route::get('treatment-sessions/today', [TreatmentSessionController::class, 'today'])->name('backend.treatment-sessions.today');
    Route::get('treatment-sessions/treatment/{treatment}', [TreatmentSessionController::class, 'treatmentSessions'])->name('backend.treatment-sessions.by-treatment');
    Route::post('treatment-sessions/quick-create/{treatment}', [TreatmentSessionController::class, 'quickCreate'])->name('backend.treatment-sessions.quick-create');
    Route::post('treatment-sessions/{session}/start', [TreatmentSessionController::class, 'start'])->name('backend.treatment-sessions.start');
    Route::post('treatment-sessions/{session}/complete', [TreatmentSessionController::class, 'complete'])->name('backend.treatment-sessions.complete');
    Route::post('treatment-sessions/{session}/cancel', [TreatmentSessionController::class, 'cancel'])->name('backend.treatment-sessions.cancel');
    Route::post('treatment-sessions/{session}/postpone', [TreatmentSessionController::class, 'postpone'])->name('backend.treatment-sessions.postpone');
    Route::post('treatment-sessions/{session}/reschedule', [TreatmentSessionController::class, 'reschedule'])->name('backend.treatment-sessions.reschedule');

    // -----------------------------
    // Prescriptions
    // -----------------------------
    Route::resource('prescriptions', PrescriptionController::class)->names('backend.prescriptions');
    Route::get('prescriptions/treatment/{treatment}', [PrescriptionController::class, 'treatmentPrescriptions'])->name('backend.prescriptions.by-treatment');
    Route::get('prescriptions/get-medicines', [PrescriptionController::class, 'getMedicines'])->name('backend.prescriptions.get-medicines');
    Route::post('prescriptions/quick-create', [PrescriptionController::class, 'quickCreate'])->name('backend.prescriptions.quick-create');
    Route::get('prescriptions/{prescription}/print', [PrescriptionController::class, 'print'])->name('backend.prescriptions.print');
    Route::post('prescriptions/{prescription}/expire', [PrescriptionController::class, 'expire'])->name('backend.prescriptions.expire');
    Route::post('prescriptions/{prescription}/cancel', [PrescriptionController::class, 'cancel'])->name('backend.prescriptions.cancel');
    Route::post('prescriptions/{prescription}/mark-as-filled', [PrescriptionController::class, 'markAsFilled'])->name('backend.prescriptions.mark-as-filled');
    Route::post('prescriptions/{prescription}/dispense-all', [PrescriptionController::class, 'dispenseAll'])->name('backend.prescriptions.dispense-all');
    Route::post('prescriptions/{prescription}/add-item', [PrescriptionController::class, 'addItem'])->name('backend.prescriptions.add-item');
    Route::post('prescription-item/{item}/dispense', [PrescriptionController::class, 'dispenseItem'])->name('backend.prescriptions.item.dispense');
    Route::post('prescription-item/{item}/cancel', [PrescriptionController::class, 'cancelItem'])->name('backend.prescriptions.item.cancel');
    Route::delete('prescription-item/{item}', [PrescriptionController::class, 'removeItem'])->name('backend.prescriptions.item.remove');

    // -----------------------------
    // Medical Files
    // -----------------------------
    Route::resource('medical-files', MedicalFileController::class)->names('backend.medical_files');
    Route::get('medical-files/patient/{patientId}', [MedicalFileController::class, 'getFilesByPatient'])->name('backend.medical_files.by-patient');
    Route::get('medical-files/{id}/download', [MedicalFileController::class, 'download'])->name('backend.medical_files.download');

    // -----------------------------
    // Inventory Stock
    // -----------------------------
    Route::resource('inventory-stock', InventoryStockController::class)->names('backend.inventory_stock');
    Route::post('inventory-stock/{id}/adjust', [InventoryStockController::class, 'adjustStock'])->name('backend.inventory_stock.adjust');
    Route::get('inventory-stock/reports/low-stock', [InventoryStockController::class, 'lowStockReport'])->name('backend.inventory_stock.reports.low_stock');
    Route::get('inventory-stock/reports/expiry', [InventoryStockController::class, 'expiryReport'])->name('backend.inventory_stock.reports.expiry');

    // -----------------------------
    // Inventory Transactions
    // -----------------------------
    Route::resource('inventory-transactions', InventoryTransactionController::class)->names('backend.inventory_transactions');
    Route::get('inventory-transactions/reports/purchases', [InventoryTransactionController::class, 'purchaseReport'])->name('backend.inventory_transactions.reports.purchase');
    Route::get('inventory-transactions/reports/consumptions', [InventoryTransactionController::class, 'consumptionReport'])->name('backend.inventory_transactions.reports.consumption');
    Route::get('inventory-transactions/{itemId}/movement', [InventoryTransactionController::class, 'stockMovement'])->name('backend.inventory_transactions.movement');

    // -----------------------------
    // Inventory Usage
    // -----------------------------
    Route::resource('inventory-usage', InventoryUsageController::class)->names('backend.inventory_usage');
    Route::get('inventory-usage/report', [InventoryUsageController::class, 'report'])->name('backend.inventory_usage.report');
    Route::get('inventory-usage/treatment/{treatmentId}', [InventoryUsageController::class, 'treatmentUsage'])->name('backend.inventory_usage.treatment');
    Route::get('inventory-usage/patient/{patientId}', [InventoryUsageController::class, 'patientUsage'])->name('backend.inventory_usage.patient');
    Route::post('inventory-usage/quick-use', [InventoryUsageController::class, 'quickUse'])->name('backend.inventory_usage.quick_use');

    // -----------------------------
    // Invoices
    // -----------------------------
    Route::resource('invoices', InvoiceController::class)->names('backend.invoices');
    Route::get('invoices/{id}/print', [InvoiceController::class, 'print'])->name('invoices.print');
    Route::post('invoices/{id}/send', [InvoiceController::class, 'send'])->name('invoices.send');
    Route::post('invoices/{id}/cancel', [InvoiceController::class, 'cancel'])->name('invoices.cancel');
    Route::post('invoices/{id}/mark-paid', [InvoiceController::class, 'markPaid'])->name('invoices.mark-paid');

    // -----------------------------
    // Invoice Items
    // -----------------------------
    Route::resource('invoice-items', InvoiceItemController::class)->names('invoice-items');
    Route::post('invoice-items/{id}/adjust', [InvoiceItemController::class, 'adjust'])->name('invoice-items.adjust');

    // -----------------------------
    // Payments
    // -----------------------------
    Route::resource('payments', PaymentController::class)->names('payments');
    Route::get('payments/{payment}/receipt', [PaymentController::class, 'receipt'])->name('payments.receipt');

    // -----------------------------
    // Payment Installments
    // -----------------------------
    Route::resource('payment-installments', PaymentInstallmentController::class)->names('payment-installments');

    // -----------------------------
    // Payment Allocations
    // -----------------------------
    Route::resource('payment-allocations', PaymentAllocationController::class)->names('payment-allocations');

    // -----------------------------
    // Receipts
    // -----------------------------
    Route::resource('receipts', ReceiptController::class)->names('receipts');
    Route::get('receipts/{receipt}/print', [ReceiptController::class, 'print'])->name('receipts.print');
    Route::post('receipts/{receipt}/cancel', [ReceiptController::class, 'cancel'])->name('receipts.cancel');
});

require __DIR__ . '/auth.php';
