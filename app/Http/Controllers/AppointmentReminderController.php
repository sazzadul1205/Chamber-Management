<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\AppointmentReminder;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AppointmentReminderController extends Controller
{
  /**
   * =========================================================================
   * REMINDERS INDEX PAGE
   * =========================================================================
   * 
   * Display all appointment reminders with filtering options.
   * Supports filtering by:
   * - Status (pending, sent, failed)
   * - Reminder type (sms, email, push, in_app)
   * - Date range
   * - Specific appointment
   * 
   * @param Request $request HTTP request with filter parameters
   * @return \Illuminate\View\View Reminders index page
   */
  public function index(Request $request)
  {
    // Build base query with appointment relationships
    $query = AppointmentReminder::with(['appointment.patient', 'appointment.doctor.user'])
      ->orderBy('scheduled_at', 'desc');

    // Apply filters based on request parameters
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

    // Filter options for dropdowns
    $statuses = ['pending', 'sent', 'failed'];
    $types = ['sms', 'email', 'push', 'in_app'];

    return view('backend.reminders.index', compact('reminders', 'statuses', 'types'));
  }

  /**
   * =========================================================================
   * CREATE REMINDER FORM
   * =========================================================================
   * 
   * Display form for creating a new reminder.
   * Pre-selects appointment if appointment_id is provided.
   * Only shows future scheduled appointments.
   * 
   * @param Request $request HTTP request with optional appointment_id
   * @return \Illuminate\View\View Reminder creation form
   */
  public function create(Request $request)
  {
    $appointmentId = $request->appointment_id;
    $appointment = null;

    // Load specific appointment if ID provided
    if ($appointmentId) {
      $appointment = Appointment::with(['patient', 'doctor.user'])->findOrFail($appointmentId);
    }

    // Get all future scheduled appointments for dropdown
    $appointments = Appointment::with(['patient', 'doctor.user'])
      ->whereIn('status', ['scheduled'])
      ->whereDate('appointment_date', '>=', today())
      ->orderBy('appointment_date')
      ->orderBy('appointment_time')
      ->get();

    // Reminder type options with display names
    $types = [
      'sms' => 'SMS Text Message',
      'email' => 'Email',
      'push' => 'Push Notification',
      'in_app' => 'In-App Notification'
    ];

    return view('backend.reminders.create', compact('appointments', 'appointment', 'types'));
  }

  /**
   * =========================================================================
   * STORE NEW REMINDER
   * =========================================================================
   * 
   * Validate and store a new appointment reminder.
   * Validates:
   * - Appointment existence
   * - Reminder type
   * - Minutes before appointment (5 minutes to 7 days)
   * - Custom message length
   * 
   * Calculates scheduled time based on minutes_before or sends immediately.
   * Prevents reminders for past appointments.
   * 
   * @param Request $request HTTP request with reminder data
   * @return \Illuminate\Http\RedirectResponse Redirect with success/error message
   */
  public function store(Request $request)
  {
    $request->validate([
      'appointment_id'    => 'required|exists:appointments,id',
      'reminder_type'     => 'required|in:sms,email,push,in_app',
      'minutes_before'    => 'required|integer|min:5|max:10080', // 5 minutes to 7 days (10080 minutes)
      'custom_message'    => 'nullable|string|max:500',
      'send_immediately'  => 'boolean'
    ]);

    $appointment = Appointment::findOrFail($request->appointment_id);

    // -------------------------------
    // VALIDATE APPOINTMENT DATE/TIME
    // -------------------------------
    // Create proper datetime object from appointment date and time
    $appointmentDateTime = Carbon::parse($appointment->appointment_date)
      ->setTimeFromTimeString($appointment->appointment_time);

    if ($appointmentDateTime->isPast()) {
      return back()->with('error', 'Cannot set reminders for past appointments.');
    }

    // -------------------------------
    // CALCULATE SCHEDULED TIME
    // -------------------------------
    if ($request->send_immediately) {
      $scheduledAt = now();
    } else {
      $scheduledAt = $appointmentDateTime->copy()->subMinutes($request->minutes_before);
    }

    // -------------------------------
    // CREATE REMINDER RECORD
    // -------------------------------
    $reminder = AppointmentReminder::create([
      'appointment_id' => $appointment->id,
      'reminder_type'  => $request->reminder_type,
      'message'        => $request->custom_message ?? $this->generateMessage($appointment, $request->reminder_type),
      'status'         => 'pending',
      'scheduled_at'   => $scheduledAt,
      'meta'           => [
        'minutes_before'   => $request->minutes_before,
        'custom_message'   => $request->filled('custom_message'),
        'created_by'       => auth()->id(),
        'created_at'       => now()->toDateTimeString()
      ]
    ]);

    // -------------------------------
    // HANDLE IMMEDIATE SEND REQUEST
    // -------------------------------
    if ($request->send_immediately) {
      try {
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

  /**
   * =========================================================================
   * SEND REMINDER MANUALLY
   * =========================================================================
   * 
   * Manually trigger sending of a pending reminder.
   * Routes to appropriate sending method based on reminder type.
   * Updates reminder status to sent or failed.
   * 
   * @param AppointmentReminder $reminder Reminder model instance
   * @return \Illuminate\Http\RedirectResponse Redirect with success/error message
   */
  public function sendNow(AppointmentReminder $reminder)
  {
    try {
      $appointment = $reminder->appointment;

      // Route to appropriate sending method
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

      // Mark as sent with metadata
      $reminder->markAsSent([
        'sent_manually_by' => auth()->id(),
        'sent_at' => now()
      ]);

      return back()->with('success', 'Reminder sent successfully.');
    } catch (\Exception $e) {
      $reminder->markAsFailed($e->getMessage());
      return back()->with('error', 'Failed to send reminder: ' . $e->getMessage());
    }
  }

  /**
   * =========================================================================
   * BULK SEND REMINDERS
   * =========================================================================
   * 
   * Send multiple reminders in bulk.
   * Only processes pending reminders.
   * Tracks success and failure counts.
   * 
   * @param Request $request HTTP request with reminder IDs array
   * @return \Illuminate\Http\RedirectResponse Redirect with summary results
   */
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

      // Process only pending reminders
      if ($reminder && $reminder->status === 'pending') {
        try {
          $appointment = $reminder->appointment;

          // Send based on type (only SMS and email supported in bulk)
          switch ($reminder->reminder_type) {
            case 'sms':
              $appointment->sendSmsReminder($reminder);
              break;
            case 'email':
              $appointment->sendEmailReminder($reminder);
              break;
          }

          $reminder->markAsSent([
            'bulk_sent' => true,
            'sent_by' => auth()->id()
          ]);
          $sent++;
        } catch (\Exception $e) {
          $reminder->markAsFailed($e->getMessage());
          $failed++;
        }
      }
    }

    return back()->with('success', "Sent: {$sent}, Failed: {$failed}");
  }

  /**
   * =========================================================================
   * RESCHEDULE REMINDER
   * =========================================================================
   * 
   * Change the scheduled time for a reminder.
   * Resets status to pending for the new scheduled time.
   * 
   * @param Request $request HTTP request with new_time parameter
   * @param AppointmentReminder $reminder Reminder model instance
   * @return \Illuminate\Http\RedirectResponse Redirect with success message
   */
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

  /**
   * =========================================================================
   * DELETE REMINDER
   * =========================================================================
   * 
   * Delete a reminder record.
   * Prevents deletion of already sent reminders.
   * 
   * @param AppointmentReminder $reminder Reminder model instance
   * @return \Illuminate\Http\RedirectResponse Redirect with success/error message
   */
  public function destroy(AppointmentReminder $reminder)
  {
    if ($reminder->status === 'sent') {
      return back()->with('error', 'Cannot delete already sent reminders.');
    }

    $reminder->delete();

    return back()->with('success', 'Reminder deleted successfully.');
  }

  /**
   * =========================================================================
   * REMINDER STATISTICS
   * =========================================================================
   * 
   * Display reminder statistics including:
   * - Total counts by status
   * - Daily statistics for past 7 days
   * - Distribution by reminder type
   * 
   * @return \Illuminate\View\View Statistics dashboard
   */
  public function stats()
  {
    // Overall statistics
    $stats = [
      'total'   => AppointmentReminder::count(),
      'sent'    => AppointmentReminder::sent()->count(),
      'pending' => AppointmentReminder::pending()->count(),
      'failed'  => AppointmentReminder::failed()->count(),
    ];

    // -------------------------------
    // DAILY STATISTICS (PAST 7 DAYS)
    // -------------------------------
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

    // -------------------------------
    // TYPE DISTRIBUTION STATISTICS
    // -------------------------------
    $typeStats = AppointmentReminder::selectRaw('
                reminder_type,
                COUNT(*) as total,
                SUM(CASE WHEN status = "sent" THEN 1 ELSE 0 END) as sent
            ')
      ->groupBy('reminder_type')
      ->get();

    return view('backend.reminders.stats', compact('stats', 'dailyStats', 'typeStats'));
  }

  /**
   * =========================================================================
   * GENERATE DEFAULT REMINDER MESSAGE
   * =========================================================================
   * 
   * Generate a default reminder message based on appointment details
   * and reminder type.
   * 
   * @param Appointment $appointment Appointment model instance
   * @param string $type Reminder type (sms, email, push, in_app)
   * @return string Generated message content
   */
  private function generateMessage($appointment, $type)
  {
    $doctorName = $appointment->doctor->user->name ?? 'Doctor';
    $patientName = $appointment->patient->name ?? 'Patient';
    $date = Carbon::parse($appointment->appointment_date)->format('M d, Y');
    $time = Carbon::parse($appointment->appointment_time)->format('h:i A');
    $typeName = ucfirst($appointment->appointment_type);

    // Generate message based on reminder type
    if ($type === 'sms') {
      return "Reminder: {$typeName} appt with Dr. {$doctorName} on {$date} at {$time}. Reply YES to confirm.";
    }

    if ($type === 'email') {
      // Return email template content
      return view('emails.appointment-reminder', [
        'appointment' => $appointment,
        'doctorName' => $doctorName,
        'patientName' => $patientName,
        'date' => $date,
        'time' => $time,
        'typeName' => $typeName
      ])->render();
    }

    // Default message for push and in-app notifications
    return "Appointment Reminder: {$typeName} with Dr. {$doctorName} on {$date} at {$time}";
  }
}
