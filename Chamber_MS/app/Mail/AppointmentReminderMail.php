<?php

namespace App\Mail;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AppointmentReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public $appointment;
    public $message;
    public $patientName;
    public $doctorName;
    public $date;
    public $time;
    public $typeName;

    /**
     * Create a new message instance.
     */
    public function __construct(Appointment $appointment, $message = null)
    {
        $this->appointment = $appointment;
        $this->message = $message;

        // Extract data for the email
        $this->patientName = $appointment->patient->name ?? 'Patient';
        $this->doctorName = $appointment->doctor->user->name ?? 'Doctor';
        $this->date = \Carbon\Carbon::parse($appointment->appointment_date)->format('M d, Y');
        $this->time = \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A');
        $this->typeName = ucfirst($appointment->appointment_type);
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject("Appointment Reminder - {$this->date} at {$this->time}")
            ->view('emails.appointment-reminder')
            ->with([
                'appointment' => $this->appointment,
                'patientName' => $this->patientName,
                'doctorName' => $this->doctorName,
                'date' => $this->date,
                'time' => $this->time,
                'typeName' => $this->typeName,
                'customMessage' => $this->message,
            ]);
    }
}
