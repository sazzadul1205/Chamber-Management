<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppointmentReminder extends Model
{
  use HasFactory;

  protected $fillable = [
    'appointment_id',
    'reminder_type',
    'message',
    'status',
    'scheduled_at',
    'sent_at',
    'meta'
  ];

  protected $casts = [
    'scheduled_at' => 'datetime',
    'sent_at' => 'datetime',
    'meta' => 'array'
  ];

  // Relationships
  public function appointment()
  {
    return $this->belongsTo(Appointment::class);
  }

  // Scopes
  public function scopePending($query)
  {
    return $query->where('status', 'pending');
  }

  public function scopeSent($query)
  {
    return $query->where('status', 'sent');
  }

  public function scopeFailed($query)
  {
    return $query->where('status', 'failed');
  }

  public function scopeDue($query)
  {
    return $query->where('scheduled_at', '<=', now());
  }

  public function scopeForAppointment($query, $appointmentId)
  {
    return $query->where('appointment_id', $appointmentId);
  }

  public function scopeByType($query, $type)
  {
    return $query->where('reminder_type', $type);
  }

  public function markAsSent($meta = null)
  {
    $this->update([
      'status' => 'sent',
      'sent_at' => now(),
      'meta' => $meta
    ]);
  }

  public function markAsFailed($error)
  {
    $this->update([
      'status' => 'failed',
      'meta' => ['error' => $error]
    ]);
  }
}
