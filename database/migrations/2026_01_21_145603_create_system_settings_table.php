<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('setting_key', 100)->unique();
            $table->text('setting_value')->nullable();
            $table->enum('setting_type', ['string', 'number', 'boolean', 'json', 'date']);
            $table->string('category', 50)->default('general');
            $table->text('description')->nullable();
            $table->boolean('is_public')->default(false);
            $table->timestamps();
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
        });

        // Insert default system settings
        DB::table('system_settings')->insert([
            // General Settings
            [
                'setting_key' => 'clinic_name',
                'setting_value' => 'Dental Care Clinic',
                'setting_type' => 'string',
                'category' => 'general',
                'description' => 'Name of the dental clinic',
                'is_public' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'setting_key' => 'clinic_address',
                'setting_value' => '123 Dental Street, City, Country',
                'setting_type' => 'string',
                'category' => 'general',
                'description' => 'Clinic address',
                'is_public' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'setting_key' => 'clinic_phone',
                'setting_value' => '+1234567890',
                'setting_type' => 'string',
                'category' => 'general',
                'description' => 'Clinic contact number',
                'is_public' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'setting_key' => 'clinic_email',
                'setting_value' => 'info@dentalclinic.com',
                'setting_type' => 'string',
                'category' => 'general',
                'description' => 'Clinic email address',
                'is_public' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'setting_key' => 'currency',
                'setting_value' => 'USD',
                'setting_type' => 'string',
                'category' => 'general',
                'description' => 'Default currency',
                'is_public' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'setting_key' => 'timezone',
                'setting_value' => 'UTC',
                'setting_type' => 'string',
                'category' => 'general',
                'description' => 'System timezone',
                'is_public' => false,
                'created_at' => now(),
                'updated_at' => now()
            ],

            // Appointment Settings
            [
                'setting_key' => 'appointment_duration',
                'setting_value' => '30',
                'setting_type' => 'number',
                'category' => 'appointment',
                'description' => 'Default appointment duration in minutes',
                'is_public' => false,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'setting_key' => 'slot_interval',
                'setting_value' => '15',
                'setting_type' => 'number',
                'category' => 'appointment',
                'description' => 'Time slot interval in minutes',
                'is_public' => false,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'setting_key' => 'max_appointments_per_day',
                'setting_value' => '50',
                'setting_type' => 'number',
                'category' => 'appointment',
                'description' => 'Maximum appointments per day',
                'is_public' => false,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'setting_key' => 'allow_online_booking',
                'setting_value' => 'true',
                'setting_type' => 'boolean',
                'category' => 'appointment',
                'description' => 'Allow online appointment booking',
                'is_public' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],

            // Billing Settings
            [
                'setting_key' => 'tax_rate',
                'setting_value' => '10',
                'setting_type' => 'number',
                'category' => 'billing',
                'description' => 'Tax rate percentage',
                'is_public' => false,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'setting_key' => 'invoice_prefix',
                'setting_value' => 'INV',
                'setting_type' => 'string',
                'category' => 'billing',
                'description' => 'Invoice number prefix',
                'is_public' => false,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'setting_key' => 'invoice_start_number',
                'setting_value' => '1000',
                'setting_type' => 'number',
                'category' => 'billing',
                'description' => 'Starting invoice number',
                'is_public' => false,
                'created_at' => now(),
                'updated_at' => now()
            ],

            // Notification Settings
            [
                'setting_key' => 'send_sms_notifications',
                'setting_value' => 'true',
                'setting_type' => 'boolean',
                'category' => 'notification',
                'description' => 'Enable SMS notifications',
                'is_public' => false,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'setting_key' => 'send_email_notifications',
                'setting_value' => 'true',
                'setting_type' => 'boolean',
                'category' => 'notification',
                'description' => 'Enable email notifications',
                'is_public' => false,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'setting_key' => 'appointment_reminder_hours',
                'setting_value' => '24',
                'setting_type' => 'number',
                'category' => 'notification',
                'description' => 'Hours before appointment to send reminder',
                'is_public' => false,
                'created_at' => now(),
                'updated_at' => now()
            ],

            // Inventory Settings
            [
                'setting_key' => 'low_stock_threshold',
                'setting_value' => '10',
                'setting_type' => 'number',
                'category' => 'inventory',
                'description' => 'Low stock threshold',
                'is_public' => false,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'setting_key' => 'auto_generate_item_code',
                'setting_value' => 'true',
                'setting_type' => 'boolean',
                'category' => 'inventory',
                'description' => 'Auto generate inventory item codes',
                'is_public' => false,
                'created_at' => now(),
                'updated_at' => now()
            ],

            // Patient Settings
            [
                'setting_key' => 'patient_code_prefix',
                'setting_value' => 'PAT',
                'setting_type' => 'string',
                'category' => 'patient',
                'description' => 'Patient code prefix',
                'is_public' => false,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'setting_key' => 'auto_generate_patient_code',
                'setting_value' => 'true',
                'setting_type' => 'boolean',
                'category' => 'patient',
                'description' => 'Auto generate patient codes',
                'is_public' => false,
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('system_settings');
    }
};
