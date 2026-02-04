<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\AppointmentReminder;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AppointmentReminderController extends Controller
{
  // =========================
  // REMINDERS INDEX PAGE
  // =========================
  public function index(Request $request)
  {
    $query = AppointmentReminder::with(['appointment.patient', 'appointment.doctor.user'])
      ->orderBy('scheduled_at', 'desc');

    // Filters
    if ($request->filled('status')) {
      $query->where('status', $request->status);
    }

    if ($request->filled('type')) {
      $query->where('reminder_type', $request->type);
    }

    if ($request->filled('date_from')) {
      $query->whereDate('scheduled_at', '>=', $request->date_from);
    }

    if ($request->filled('date_to')) {
      $query->whereDate('scheduled_at', '<=', $request->date_to);
    }

    if ($request->filled('appointment_id')) {
      $query->where('appointment_id', $request->appointment_id);
    }

    $reminders = $query->paginate(50);

    $statuses = ['pending', 'sent', 'failed'];
    $types = ['sms', 'email', 'push', 'in_app'];

    return view('backend.reminders.index', compact('reminders', 'statuses', 'types'));
  }

  // =========================
  // CREATE REMINDER FORM
  // =========================
  public function create(Request $request)
  {
    $appointmentId = $request->appointment_id;
    $appointment = null;

    if ($appointmentId) {
      $appointment = Appointment::with(['patient', 'doctor.user'])->findOrFail($appointmentId);
    }

    $appointments = Appointment::with(['patient', 'doctor.user'])
      ->whereIn('status', ['scheduled'])
      ->whereDate('appointment_date', '>=', today())
      ->orderBy('appointment_date')
      ->orderBy('appointment_time')
      ->get();

    $types = [
      'sms' => 'SMS Text Message',
      'email' => 'Email',
      'push' => 'Push Notification',
      'in_app' => 'In-App Notification'
    ];

    return view('backend.reminders.create', compact('appointments', 'appointment', 'types'));
  }

  // =========================
  // STORE NEW REMINDER
  // =========================
  public function store(Request $request)
  {
    $request->validate([
      'appointment_id' => 'required|exists:appointments,id',
      'reminder_type' => 'required|in:sms,email,push,in_app',
      'minutes_before' => 'required|integer|min:5|max:10080', // max 7 days
      'custom_message' => 'nullable|string|max:500',
      'send_immediately' => 'boolean'
    ]);

    $appointment = Appointment::findOrFail($request->appointment_id);

    // Check if appointment is in the future
    // FIX: Create proper datetime object
    $appointmentDateTime = Carbon::parse($appointment->appointment_date)
      ->setTimeFromTimeString($appointment->appointment_time);

    // Alternative if appointment_time is already a time string:
    // $appointmentDateTime = Carbon::parse($appointment->appointment_date . ' ' . $appointment->appointment_time);

    if ($appointmentDateTime->isPast()) {
      return back()->with('error', 'Cannot set reminders for past appointments.');
    }

    // Calculate scheduled time
    if ($request->send_immediately) {
      $scheduledAt = now();
    } else {
      $scheduledAt = $appointmentDateTime->copy()->subMinutes($request->minutes_before);
    }

    // Create reminder
    $reminder = AppointmentReminder::create([
      'appointment_id' => $appointment->id,
      'reminder_type' => $request->reminder_type,
      'message' => $request->custom_message ?? $this->generateMessage($appointment, $request->reminder_type),
      'status' => $request->send_immediately ? 'pending' : 'pending',
      'scheduled_at' => $scheduledAt,
      'meta' => [
        'minutes_before' => $request->minutes_before,
        'custom_message' => $request->filled('custom_message'),
        'created_by' => auth()->id(),
        'created_at' => now()->toDateTimeString()
      ]
    ]);

    // If sending immediately, trigger sending
    if ($request->send_immediately) {
      try {
        // Make sure we pass the reminder to send
        $appointment->sendReminders();
        return redirect()->route('backend.reminders.index')
          ->with('success', 'Reminder sent successfully.');
      } catch (\Exception $e) {
        return redirect()->route('backend.reminders.index')
          ->with('warning', 'Reminder created but sending failed: ' . $e->getMessage());
      }
    }

    return redirect()->route('backend.reminders.index')
      ->with('success', 'Reminder scheduled successfully for ' . $scheduledAt->format('M d, Y h:i A'));
  }

  // =========================
  // SEND REMINDER MANUALLY
  // =========================
  public function sendNow(AppointmentReminder $reminder)
  {
    try {
      $appointment = $reminder->appointment;

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

      $reminder->markAsSent(['sent_manually_by' => auth()->id(), 'sent_at' => now()]);

      return back()->with('success', 'Reminder sent successfully.');
    } catch (\Exception $e) {
      $reminder->markAsFailed($e->getMessage());
      return back()->with('error', 'Failed to send reminder: ' . $e->getMessage());
    }
  }

  // =========================
  // BULK SEND REMINDERS
  // =========================
  public function bulkSend(Request $request)
  {
    $request->validate([
      'reminder_ids' => 'required|array',
      'reminder_ids.*' => 'exists:appointment_reminders,id'
    ]);

    $sent = 0;
    $failed = 0;

    foreach ($request->reminder_ids as $reminderId) {
      $reminder = AppointmentReminder::find($reminderId);

      if ($reminder && $reminder->status === 'pending') {
        try {
          $appointment = $reminder->appointment;

          switch ($reminder->reminder_type) {
            case 'sms':
              $appointment->sendSmsReminder($reminder);
              break;
            case 'email':
              $appointment->sendEmailReminder($reminder);
              break;
          }

          $reminder->markAsSent(['bulk_sent' => true, 'sent_by' => auth()->id()]);
          $sent++;
        } catch (\Exception $e) {
          $reminder->markAsFailed($e->getMessage());
          $failed++;
        }
      }
    }

    return back()->with('success', "Sent: {$sent}, Failed: {$failed}");
  }

  // =========================
  // RESCHEDULE REMINDER
  // =========================
  public function reschedule(Request $request, AppointmentReminder $reminder)
  {
    $request->validate([
      'new_time' => 'required|date|after:now'
    ]);

    $reminder->update([
      'scheduled_at' => Carbon::parse($request->new_time),
      'status' => 'pending'
    ]);

    return back()->with('success', 'Reminder rescheduled successfully.');
  }

  // =========================
  // DELETE REMINDER
  // =========================
  public function destroy(AppointmentReminder $reminder)
  {
    if ($reminder->status === 'sent') {
      return back()->with('error', 'Cannot delete already sent reminders.');
    }

    $reminder->delete();

    return back()->with('success', 'Reminder deleted successfully.');
  }

  // =========================
  // REMINDER STATS
  // =========================
  public function stats()
  {
    $stats = [
      'total' => AppointmentReminder::count(),
      'sent' => AppointmentReminder::sent()->count(),
      'pending' => AppointmentReminder::pending()->count(),
      'failed' => AppointmentReminder::failed()->count(),
    ];

    // Daily stats for the past 7 days
    $dailyStats = AppointmentReminder::selectRaw('
                DATE(created_at) as date,
                COUNT(*) as total,
                SUM(CASE WHEN status = "sent" THEN 1 ELSE 0 END) as sent,
                SUM(CASE WHEN status = "failed" THEN 1 ELSE 0 END) as failed
            ')
      ->where('created_at', '>=', now()->subDays(7))
      ->groupBy('date')
      ->orderBy('date')
      ->get();

    // Type distribution
    $typeStats = AppointmentReminder::selectRaw('
                reminder_type,
                COUNT(*) as total,
                SUM(CASE WHEN status = "sent" THEN 1 ELSE 0 END) as sent
            ')
      ->groupBy('reminder_type')
      ->get();

    return view('backend.reminders.stats', compact('stats', 'dailyStats', 'typeStats'));
  }

  // =========================
  // GENERATE MESSAGE
  // =========================
  private function generateMessage($appointment, $type)
  {
    $doctorName = $appointment->doctor->user->name ?? 'Doctor';
    $patientName = $appointment->patient->name ?? 'Patient';
    $date = Carbon::parse($appointment->appointment_date)->format('M d, Y');
    $time = Carbon::parse($appointment->appointment_time)->format('h:i A');
    $typeName = ucfirst($appointment->appointment_type);

    if ($type === 'sms') {
      return "Reminder: {$typeName} appt with Dr. {$doctorName} on {$date} at {$time}. Reply YES to confirm.";
    }

    if ($type === 'email') {
      return view('emails.appointment-reminder', [
        'appointment' => $appointment,
        'doctorName' => $doctorName,
        'patientName' => $patientName,
        'date' => $date,
        'time' => $time,
        'typeName' => $typeName
      ])->render();
    }

    return "Appointment Reminder: {$typeName} with Dr. {$doctorName} on {$date} at {$time}";
  }
}
