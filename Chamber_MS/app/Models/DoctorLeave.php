<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DoctorLeave extends Model
{
    use HasFactory;

    protected $fillable = [
        'doctor_id',
        'leave_date',
        'type',
        'reason',
        'notes',
        'status',
        'approved_by',
        'approved_at',
        'rejection_reason',
    ];

    protected $casts = [
        'leave_date' => 'date',
        'approved_at' => 'datetime',
    ];

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Accessors
    public function getFormattedDateAttribute()
    {
        return $this->leave_date->format('d M, Y');
    }

    public function getFormattedTypeAttribute()
    {
        return match ($this->type) {
            'full_day' => 'Full Day',
            'half_day' => 'Half Day',
            'emergency' => 'Emergency',
            default => $this->type,
        };
    }

    public function getStatusColorAttribute()
    {
        return match ($this->status) {
            'pending' => 'bg-yellow-500 text-white',
            'approved' => 'bg-green-500 text-white',
            'rejected' => 'bg-red-500 text-white',
            'cancelled' => 'bg-gray-500 text-white',
            default => 'bg-gray-300 text-black',
        };
    }

    public function getStatusTextAttribute()
    {
        return ucfirst($this->status);
    }

    public function getDurationAttribute()
    {
        return match ($this->type) {
            'full_day' => 'Full Day',
            'half_day' => 'Half Day',
            'emergency' => 'Emergency',
            default => 'Unknown',
        };
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeUpcoming($query)
    {
        return $query->whereDate('leave_date', '>=', now());
    }
}
