<?php

namespace App\Console\Commands;

use App\Models\AppointmentReminder;
use Illuminate\Console\Command;

class SendAppointmentReminders extends Command
{
  protected $signature = 'appointments:send-reminders';
  protected $description = 'Send pending appointment reminders';

  public function handle()
  {
    $this->info('Starting appointment reminder sending...');

    $pendingReminders = AppointmentReminder::with(['appointment.patient', 'appointment.doctor.user'])
      ->pending()
      ->where('scheduled_at', '<=', now())
      ->get();

    $this->info("Found {$pendingReminders->count()} pending reminders to send.");

    $sent = 0;
    $failed = 0;

    foreach ($pendingReminders as $reminder) {
      try {
        $appointment = $reminder->appointment;

        if (!$appointment) {
          $reminder->markAsFailed('Appointment not found');
          $failed++;
          continue;
        }

        switch ($reminder->reminder_type) {
          case 'sms':
            $appointment->sendSmsReminder($reminder);
            break;
          case 'email':
            $appointment->sendEmailReminder($reminder);
            break;
          case 'push':
            $appointment->sendPushReminder($reminder);
            break;
        }

        $reminder->markAsSent(['auto_sent' => true]);
        $sent++;

        $this->info("Sent reminder #{$reminder->id} for appointment #{$appointment->id}");
      } catch (\Exception $e) {
        $reminder->markAsFailed($e->getMessage());
        $failed++;
        $this->error("Failed to send reminder #{$reminder->id}: " . $e->getMessage());
      }
    }

    $this->info("Completed! Sent: {$sent}, Failed: {$failed}");

    return Command::SUCCESS;
  }
}
