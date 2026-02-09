<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    AppointmentController,
    AppointmentReminderController,
    BackupController,
    DashboardController,
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
    ReferralController,
    RoleController,
    SystemSettingController,
    TreatmentController,
    TreatmentProcedureController,
    TreatmentSessionController,
    UserController,
};

// =============================================================================
// PUBLIC ROUTES
// =============================================================================
Route::get('/', fn() => view('auth.login'));

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('backend.dashboard');

// =============================================================================
// AUTHENTICATED ROUTES
// =============================================================================
Route::middleware(['auth'])->group(function () {
    // =========================================================================
    // ACCESSIBLE TO ALL AUTHENTICATED USERS
    // =========================================================================

    // Profile Routes
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
    });

    // =========================================================================
    // SUPER ADMIN & ADMIN ONLY ROUTES
    // =========================================================================
    Route::middleware(['role:Super Admin,Admin'])->group(function () {
        // Roles Management
        Route::resource('roles', RoleController::class)
            ->except(['show', 'create', 'edit'])
            ->names('backend.roles');

        Route::post('roles/{role}/restore', [RoleController::class, 'restore'])
            ->name('backend.roles.restore');
        Route::delete('roles/{role}/force', [RoleController::class, 'forceDelete'])
            ->name('backend.roles.force');

        // System Settings
        Route::get('/settings', [SystemSettingController::class, 'index'])
            ->name('backend.system-settings.index');
        Route::post('/settings', [SystemSettingController::class, 'bulkUpdate'])
            ->name('backend.system-settings.bulk-update');

        // Users Management
        Route::resource('users', UserController::class)
            ->names('backend.user');
        Route::patch('/backend/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])
            ->name('backend.user.toggle-status');
        Route::post('users/{user}/reset-password', [UserController::class, 'resetPassword'])
            ->name('backend.user.reset-password');
        Route::post('users/{user}/restore', [UserController::class, 'restore'])
            ->name('backend.user.restore');
        Route::delete('users/{user}/force', [UserController::class, 'forceDelete'])
            ->name('backend.user.force');
    });

    // =========================================================================
    // SUPER ADMIN, ADMIN, DOCTOR ROUTES
    // =========================================================================
    Route::middleware(['role:Super Admin,Admin,Doctor'])->group(function () {
        // Procedure Catalog
        Route::resource('procedure-catalog', ProcedureCatalogController::class)
            ->names('backend.procedure-catalog');
        Route::get('procedure-catalog/import', [ProcedureCatalogController::class, 'import'])
            ->name('backend.procedure-catalog.import');
        Route::get('procedure-catalog/autocomplete', [ProcedureCatalogController::class, 'autocomplete'])
            ->name('backend.procedure-catalog.autocomplete');
        Route::get('backend/treatment-procedures/catalog-search', [TreatmentProcedureController::class, 'catalogSearch'])
            ->name('backend.treatment-procedures.get-catalog-procedures');

        // Diagnosis Codes
        Route::resource('diagnosis-codes', DiagnosisCodeController::class)
            ->names('backend.diagnosis-codes');
        Route::get('diagnosis-codes/export', [DiagnosisCodeController::class, 'export'])
            ->name('backend.diagnosis-codes.export');
        Route::get('diagnosis-codes/autocomplete', [DiagnosisCodeController::class, 'autocomplete'])
            ->name('backend.diagnosis-codes.autocomplete');
        Route::post('diagnosis-codes/quick-add', [DiagnosisCodeController::class, 'quickAdd'])
            ->name('backend.diagnosis-codes.quick-add');

        // Dental Charts
        Route::resource('dental-charts', DentalChartController::class)
            ->names('backend.dental-charts');
        Route::get('dental-charts/patient/{patient}/chart', [DentalChartController::class, 'patientChart'])
            ->name('backend.dental-charts.patient-chart');
        Route::post('dental-charts/quick-add', [DentalChartController::class, 'quickAdd'])
            ->name('backend.dental-charts.quick-add');
        Route::get('dental-charts/api/patient/{patient}/chart-data', [DentalChartController::class, 'getPatientChartData'])
            ->name('backend.dental-charts.patient-chart-data');

        // Doctors Management
        Route::prefix('doctors')->middleware('auth')->group(function () {
            // Utilities
            Route::get('generate-code', [DoctorController::class, 'generateCode'])
                ->name('backend.doctors.generate-code');
            Route::get('check-availability', [DoctorController::class, 'checkAvailability'])
                ->name('backend.doctors.check-availability');

            // Leave Management
            Route::get('leave-requests', [DoctorController::class, 'leaveRequests'])
                ->name('backend.doctors.leave-requests');
            Route::get('my-leaves', [DoctorController::class, 'myLeaves'])
                ->name('backend.doctors.my-leaves');
            Route::post('apply-leave', [DoctorController::class, 'applyLeave'])
                ->name('backend.doctors.apply-leave');

            // Leave Actions
            Route::prefix('leaves')->group(function () {
                Route::post('{leave}/process', [DoctorController::class, 'processLeave'])
                    ->name('backend.doctors.process-leave');
                Route::post('{leave}/cancel', [DoctorController::class, 'cancelLeave'])
                    ->name('backend.doctors.cancel-leave');
            });
        });

        // Doctor-Specific Routes
        Route::prefix('doctors/{doctor}')->middleware('auth')->group(function () {
            Route::get('schedule-management', [DoctorController::class, 'scheduleManagement'])
                ->name('backend.doctors.schedule-management');
            Route::post('update-schedule', [DoctorController::class, 'updateSchedule'])
                ->name('backend.doctors.update-schedule');
            Route::get('calendar', [DoctorController::class, 'calendar'])
                ->name('backend.doctors.calendar');
        });

        // Doctor Resource Routes
        Route::resource('doctors', DoctorController::class)
            ->names('backend.doctors')
            ->middleware('auth');

        // Prescriptions
        Route::resource('prescriptions', PrescriptionController::class)
            ->names('backend.prescriptions');
        Route::get('prescriptions/treatment/{treatment}', [PrescriptionController::class, 'treatmentPrescriptions'])
            ->name('backend.prescriptions.by-treatment');
        Route::get('prescriptions/get-medicines', [PrescriptionController::class, 'getMedicines'])
            ->name('backend.prescriptions.get-medicines');
        Route::post('prescriptions/quick-create', [PrescriptionController::class, 'quickCreate'])
            ->name('backend.prescriptions.quick-create');
        Route::get('prescriptions/{prescription}/print', [PrescriptionController::class, 'print'])
            ->name('backend.prescriptions.print');

        // Prescription Status Actions
        Route::post('prescriptions/{prescription}/expire', [PrescriptionController::class, 'expire'])
            ->name('backend.prescriptions.expire');
        Route::post('prescriptions/{prescription}/cancel', [PrescriptionController::class, 'cancel'])
            ->name('backend.prescriptions.cancel');
        Route::post('prescriptions/{prescription}/mark-as-filled', [PrescriptionController::class, 'markAsFilled'])
            ->name('backend.prescriptions.mark-as-filled');
        Route::post('prescriptions/{prescription}/dispense-all', [PrescriptionController::class, 'dispenseAll'])
            ->name('backend.prescriptions.dispense-all');

        // Prescription Item Actions
        Route::post('prescriptions/{prescription}/add-item', [PrescriptionController::class, 'addItem'])
            ->name('backend.prescriptions.add-item');
        Route::post('prescription-item/{item}/dispense', [PrescriptionController::class, 'dispenseItem'])
            ->name('backend.prescriptions.item.dispense');
        Route::post('prescription-item/{item}/cancel', [PrescriptionController::class, 'cancelItem'])
            ->name('backend.prescriptions.item.cancel');
        Route::delete('prescription-item/{item}', [PrescriptionController::class, 'removeItem'])
            ->name('backend.prescriptions.item.remove');

        // Treatments
        Route::resource('treatments', TreatmentController::class)
            ->names('backend.treatments');
        Route::post('treatments/quick-create', [TreatmentController::class, 'quickCreate'])
            ->name('backend.treatments.quick-create');
        Route::get('treatments/patient/{patientId}', [TreatmentController::class, 'patientTreatments'])
            ->name('backend.treatments.patient-treatments');

        // Treatment Actions
        Route::prefix('treatments/{treatment}')->name('backend.treatments.')->group(function () {
            Route::post('start', [TreatmentController::class, 'start'])->name('start');
            Route::post('complete', [TreatmentController::class, 'complete'])->name('complete');
            Route::post('cancel', [TreatmentController::class, 'cancel'])->name('cancel');
            Route::post('hold', [TreatmentController::class, 'hold'])->name('hold');
            Route::post('resume', [TreatmentController::class, 'resume'])->name('resume');

            // Treatment Sessions
            Route::get('sessions', [TreatmentSessionController::class, 'treatmentSessions'])
                ->name('sessions.index');
            Route::post('sessions/quick-create', [TreatmentSessionController::class, 'quickCreate'])
                ->name('sessions.quick-create');
            Route::get('sessions/create', [TreatmentSessionController::class, 'createForTreatment'])
                ->name('sessions.create');

            // Payment Routes
            Route::get('session-payments', [TreatmentController::class, 'sessionPayments'])
                ->name('session-payments');
            Route::get('procedure-payments', [TreatmentController::class, 'procedurePayments'])
                ->name('procedure-payments');
        });

        // Treatment Procedures
        Route::resource('treatment-procedures', TreatmentProcedureController::class)
            ->names('backend.treatment-procedures');
        Route::get('treatment-procedures/catalog/search', [TreatmentProcedureController::class, 'getCatalogProcedures'])
            ->name('backend.treatment-procedures.catalog.search');
        Route::get('treatment-procedures/create/{treatment}', [TreatmentProcedureController::class, 'create'])
            ->name('backend.treatment-procedures.create-for-treatment');
        Route::post('treatment-procedures/{treatment}/bulk-add', [TreatmentProcedureController::class, 'bulkAdd'])
            ->name('backend.treatment-procedures.bulk-add');
        Route::get('treatment-procedures/treatment/{treatment}', [TreatmentProcedureController::class, 'treatmentProcedures'])
            ->name('backend.treatment-procedures.by-treatment');
        Route::post('treatment-procedures/{treatmentProcedure}/start', [TreatmentProcedureController::class, 'start'])
            ->name('backend.treatment-procedures.start');
        Route::post('treatment-procedures/{treatmentProcedure}/complete', [TreatmentProcedureController::class, 'complete'])
            ->name('backend.treatment-procedures.complete');
        Route::post('treatment-procedures/{treatmentProcedure}/cancel', [TreatmentProcedureController::class, 'cancel'])
            ->name('backend.treatment-procedures.cancel');

        // Treatment Sessions
        Route::get('treatment-sessions/today', [TreatmentSessionController::class, 'today'])
            ->name('backend.treatment-sessions.today');
        Route::resource('treatment-sessions', TreatmentSessionController::class)
            ->names('backend.treatment-sessions');

        // Treatment Session Actions
        Route::prefix('treatment-sessions/{session}')->name('backend.treatment-sessions.')->group(function () {
            Route::post('start', [TreatmentSessionController::class, 'start'])->name('start');
            Route::post('complete', [TreatmentSessionController::class, 'complete'])->name('complete');
            Route::post('cancel', [TreatmentSessionController::class, 'cancel'])->name('cancel');
            Route::post('postpone', [TreatmentSessionController::class, 'postpone'])->name('postpone');
            Route::post('reschedule', [TreatmentSessionController::class, 'reschedule'])->name('reschedule');
        });

        // Medical Files
        Route::prefix('medical-files')->name('backend.medical-files.')->group(function () {
            Route::get('/', [MedicalFileController::class, 'index'])->name('index');
            Route::get('/create', [MedicalFileController::class, 'create'])->name('create');
            Route::post('/', [MedicalFileController::class, 'store'])->name('store');
            Route::get('/{medicalFile}', [MedicalFileController::class, 'show'])->name('show');
            Route::get('/{medicalFile}/edit', [MedicalFileController::class, 'edit'])->name('edit');
            Route::put('/{medicalFile}', [MedicalFileController::class, 'update'])->name('update');
            Route::delete('/{medicalFile}', [MedicalFileController::class, 'destroy'])->name('destroy');

            // Additional Actions
            Route::post('/{medicalFile}/upload-result', [MedicalFileController::class, 'uploadResult'])
                ->name('upload-result');
            Route::patch('/{medicalFile}/mark-pending', [MedicalFileController::class, 'markAsPending'])
                ->name('mark-pending');
            Route::patch('/{medicalFile}/cancel', [MedicalFileController::class, 'cancel'])
                ->name('cancel');
            Route::get('/{medicalFile}/download', [MedicalFileController::class, 'download'])
                ->name('download');
        });
    });

    // =========================================================================
    // RECEPTIONIST, DOCTOR, ADMIN, SUPER ADMIN ROUTES
    // =========================================================================
    Route::middleware(['role:Receptionist,Doctor,Admin,Super Admin'])->group(function () {
        // Patients & Families
        Route::resource('backend/patients', PatientController::class)
            ->names('backend.patients');
        Route::post('backend/patients/quick-add', [PatientController::class, 'quickAdd'])
            ->name('backend.patients.quick_add');
        Route::get('backend/patients/search/ajax', [PatientController::class, 'search'])
            ->name('backend.patients.search');
        Route::get('backend/patients/{patient}/medical-history', [PatientController::class, 'medicalHistory'])
            ->name('backend.patients.medical_history');
        Route::patch('backend/patients/{patient}/toggle-status', [PatientController::class, 'toggleStatus'])
            ->name('backend.patients.toggle-status');
        Route::post('/patients/quick-add', [PatientController::class, 'quickAdd'])
            ->name('backend.patients.quick-add');

        // Patient Families
        Route::resource('patient-families', PatientFamilyController::class)
            ->names('backend.patient-families');
        Route::get('patient-families/generate-code', [PatientFamilyController::class, 'generateCode'])
            ->name('backend.patient-families.generate-code');
        Route::post('patient-families/{patientFamily}/members', [PatientFamilyController::class, 'addMember'])
            ->name('backend.patient-families.members.add');
        Route::delete('patient-families/{patientFamily}/members/{patient}', [PatientFamilyController::class, 'removeMember'])
            ->name('backend.patient-families.members.remove');
        Route::post('patient-families/{patientFamily}/set-head/{patient}', [PatientFamilyController::class, 'setHead'])
            ->name('backend.patient-families.set-head');

        // Referral Tracking
        Route::prefix('referrals')->name('backend.referrals.')->group(function () {
            Route::get('/', [ReferralController::class, 'index'])->name('index');
            Route::get('/report', [ReferralController::class, 'report'])->name('report');
            Route::get('/{patient}', [ReferralController::class, 'show'])->name('show');
        });

        // Appointments
        Route::get('appointments/calendar', [AppointmentController::class, 'calendar'])
            ->name('backend.appointments.calendar');
        Route::get('appointments/today', [AppointmentController::class, 'today'])
            ->name('backend.appointments.today');
        Route::get('appointments/api/available-slots', [AppointmentController::class, 'getAvailableSlots'])
            ->name('backend.appointments.available-slots');
        Route::post('appointments/{appointment}/check-in', [AppointmentController::class, 'checkIn'])
            ->name('backend.appointments.check-in');
        Route::post('appointments/{appointment}/start', [AppointmentController::class, 'start'])
            ->name('backend.appointments.start');
        Route::post('appointments/{appointment}/complete', [AppointmentController::class, 'complete'])
            ->name('backend.appointments.complete');
        Route::post('appointments/{appointment}/cancel', [AppointmentController::class, 'cancel'])
            ->name('backend.appointments.cancel');
        Route::put('appointments/{appointment}/reschedule', [AppointmentController::class, 'reschedule'])
            ->name('backend.appointments.reschedule');
        Route::post('appointments/{appointment}/no-show', [AppointmentController::class, 'noShow'])
            ->name('backend.appointments.no-show');
        Route::get('appointments/{appointment}/details', [AppointmentController::class, 'getDetails'])
            ->name('backend.appointments.details');
        Route::get('appointments/queue', [AppointmentController::class, 'queue'])
            ->name('backend.appointments.queue');

        // Walk-in Appointments
        Route::get('appointments/walk-in', [AppointmentController::class, 'walkInForm'])
            ->name('backend.appointments.walk-in');
        Route::post('appointments/walk-in', [AppointmentController::class, 'walkInStore'])
            ->name('backend.appointments.walk-in.store');
        Route::resource('appointments', AppointmentController::class)
            ->names('backend.appointments');

        // Appointment Reminders
        Route::prefix('reminders')->name('backend.reminders.')->group(function () {
            Route::get('/', [AppointmentReminderController::class, 'index'])->name('index');
            Route::get('/create', [AppointmentReminderController::class, 'create'])->name('create');
            Route::post('/', [AppointmentReminderController::class, 'store'])->name('store');
            Route::post('/bulk-send', [AppointmentReminderController::class, 'bulkSend'])
                ->name('bulk-send');
            Route::post('/{reminder}/send-now', [AppointmentReminderController::class, 'sendNow'])
                ->name('send-now');
            Route::get('/stats', [AppointmentReminderController::class, 'stats'])->name('stats');
            Route::delete('/{reminder}', [AppointmentReminderController::class, 'destroy'])
                ->name('destroy');
        });

        // Dental Chairs
        Route::resource('dental-chairs', DentalChairController::class)
            ->names('backend.dental-chairs');
        Route::get('dental-chairs/dashboard', [DentalChairController::class, 'dashboard'])
            ->name('backend.dental-chairs.dashboard');
        Route::get('dental-chairs/schedule', [DentalChairController::class, 'schedule'])
            ->name('backend.dental-chairs.schedule');
        Route::get('dental-chairs/generate-code', [DentalChairController::class, 'generateCode'])
            ->name('backend.dental-chairs.generate-code');
        Route::get('dental-chairs/api/available-chairs', [DentalChairController::class, 'getAvailableChairs'])
            ->name('backend.dental-chairs.available-chairs');
        Route::post('dental-chairs/{dentalChair}/update-status', [DentalChairController::class, 'updateStatus'])
            ->name('backend.dental-chairs.update-status');
        Route::post('dental-chairs/{dentalChair}/quick-status-change', [DentalChairController::class, 'quickStatusChange'])
            ->name('backend.dental-chairs.quick-status-change');
    });

    // =========================================================================
    // ACCOUNTANT, ADMIN, SUPER ADMIN ROUTES
    // =========================================================================
    Route::middleware(['role:Accountant,Admin,Super Admin'])->group(function () {
        // Invoices
        Route::resource('invoices', InvoiceController::class)
            ->names('backend.invoices');
        Route::get('invoices/{id}/print', [InvoiceController::class, 'print'])
            ->name('invoices.print');
        Route::post('invoices/{id}/send', [InvoiceController::class, 'send'])
            ->name('invoices.send');
        Route::post('invoices/{id}/cancel', [InvoiceController::class, 'cancel'])
            ->name('invoices.cancel');
        Route::post('invoices/{id}/mark-paid', [InvoiceController::class, 'markPaid'])
            ->name('invoices.mark-paid');

        // Invoice Items
        Route::get('/invoices/treatment/{treatment}/invoice', [InvoiceController::class, 'treatmentInvoice'])
            ->name('invoices.treatment-invoice');
        Route::get('/invoices/treatment/{treatment}/download', [InvoiceController::class, 'downloadTreatmentInvoice'])
            ->name('invoices.download-treatment-invoice');
        Route::resource('invoice-items', InvoiceItemController::class)
            ->names('invoice-items');
        Route::post('invoice-items/{id}/adjust', [InvoiceItemController::class, 'adjust'])
            ->name('invoice-items.adjust');

        // Payments
        Route::post('/payments/session', [PaymentController::class, 'storeSessionPayment'])
            ->name('backend.payments.store-session');
        Route::post('/payments/procedure', [PaymentController::class, 'storeProcedurePayment'])
            ->name('backend.payments.store-procedure');
        Route::get('treatments/{treatment}/payments', [PaymentController::class, 'treatmentPayments'])
            ->name('payments.treatment-payments');
        Route::post('payments/overall-payment', [PaymentController::class, 'storeOverallPayment'])
            ->name('payments.store-overall-payment');
        Route::post('payments/treatment-payment', [PaymentController::class, 'storeTreatmentPayment'])
            ->name('payments.store-treatment-payment');
        Route::resource('payments', PaymentController::class)
            ->names('backend.payments');
        Route::get('payments/{payment}/receipt', [PaymentController::class, 'receipt'])
            ->name('backend.payments.receipt');

        // Payment Installments
        Route::resource('payment-installments', PaymentInstallmentController::class)
            ->names('payment-installments');

        // Payment Allocations
        Route::resource('payment-allocations', PaymentAllocationController::class)
            ->names('payment-allocations');

        // Receipts
        Route::resource('receipts', ReceiptController::class)
            ->names('receipts');
        Route::get('receipts/{receipt}/print', [ReceiptController::class, 'print'])
            ->name('receipts.print');
        Route::post('receipts/{receipt}/cancel', [ReceiptController::class, 'cancel'])
            ->name('receipts.cancel');
    });

    // =========================================================================
    // INVENTORY MANAGEMENT ROUTES
    // =========================================================================
    Route::middleware(['role:Accountant,Admin,Super Admin,Doctor'])->group(function () {
        // Inventory Items
        Route::resource('inventory-items', InventoryItemController::class)
            ->names('backend.inventory-items');
        Route::get('inventory-items/export', [InventoryItemController::class, 'export'])
            ->name('backend.inventory-items.export');
        Route::get('inventory-items/autocomplete', [InventoryItemController::class, 'autocomplete'])
            ->name('backend.inventory-items.autocomplete');
        Route::get('inventory-items/generate-code', [InventoryItemController::class, 'generateCode'])
            ->name('backend.inventory-items.generate-code');

        // Medicines
        Route::resource('medicines', MedicineController::class)
            ->names('backend.medicines');
        Route::get('medicines/export', [MedicineController::class, 'export'])
            ->name('backend.medicines.export');
        Route::get('medicines/import', [MedicineController::class, 'import'])
            ->name('backend.medicines.import');
        Route::post('medicines/process-import', [MedicineController::class, 'processImport'])
            ->name('backend.medicines.process-import');
        Route::get('medicines/autocomplete', [MedicineController::class, 'autocomplete'])
            ->name('backend.medicines.autocomplete');
        Route::get('medicines/generate-code', [MedicineController::class, 'generateCode'])
            ->name('backend.medicines.generate-code');

        // Inventory Stock
        Route::resource('inventory-stock', InventoryStockController::class)
            ->names('backend.inventory_stock');
        Route::post('inventory-stock/{id}/adjust', [InventoryStockController::class, 'adjustStock'])
            ->name('backend.inventory_stock.adjust');
        Route::get('inventory-stock/reports/low-stock', [InventoryStockController::class, 'lowStockReport'])
            ->name('backend.inventory_stock.reports.low_stock');
        Route::get('inventory-stock/reports/expiry', [InventoryStockController::class, 'expiryReport'])
            ->name('backend.inventory_stock.reports.expiry');

        // Inventory Transactions
        Route::resource('inventory-transactions', InventoryTransactionController::class)
            ->names('backend.inventory_transactions');
        Route::get('inventory-transactions/reports/purchases', [InventoryTransactionController::class, 'purchaseReport'])
            ->name('backend.inventory_transactions.reports.purchase');
        Route::get('inventory-transactions/reports/consumptions', [InventoryTransactionController::class, 'consumptionReport'])
            ->name('backend.inventory_transactions.reports.consumption');
        Route::get('inventory-transactions/{itemId}/movement', [InventoryTransactionController::class, 'stockMovement'])
            ->name('backend.inventory_transactions.movement');

        // Inventory Usage
        Route::resource('inventory-usage', InventoryUsageController::class)
            ->names('backend.inventory_usage');
        Route::get('inventory-usage/report', [InventoryUsageController::class, 'report'])
            ->name('backend.inventory_usage.report');
        Route::get('inventory-usage/treatment/{treatmentId}', [InventoryUsageController::class, 'treatmentUsage'])
            ->name('backend.inventory_usage.treatment');
        Route::get('inventory-usage/patient/{patientId}', [InventoryUsageController::class, 'patientUsage'])
            ->name('backend.inventory_usage.patient');
        Route::post('inventory-usage/quick-use', [InventoryUsageController::class, 'quickUse'])
            ->name('backend.inventory_usage.quick_use');
    });

    // Backup & Restore Routes
    Route::middleware(['role:Super Admin,Admin'])->group(function () {
        Route::prefix('backup')->name('backend.backup.')->group(function () {
            Route::get('/', [BackupController::class, 'index'])->name('index');
            Route::post('/create', [BackupController::class, 'createBackup'])->name('create');
            Route::get('/download/{backupName}', [BackupController::class, 'downloadBackup'])->name('download');
            Route::post('/restore/{backupName}', [BackupController::class, 'restoreBackup'])->name('restore');
            Route::delete('/delete/{backupName}', [BackupController::class, 'deleteBackup'])->name('delete');
            Route::get('/system-info', [BackupController::class, 'systemInfo'])->name('system-info');
        });
    });
});

require __DIR__ . '/auth.php';
