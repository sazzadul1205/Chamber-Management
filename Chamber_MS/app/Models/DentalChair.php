<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DentalChair extends Model
{
    use HasFactory;

    // =========================
    // FILLABLE & CASTS
    // =========================
    protected $fillable = [
        'chair_code',
        'name',
        'location',
        'status',
        'last_used',
        'notes'
    ];

    protected $casts = [
        'last_used' => 'datetime'
    ];

    // =========================
    // SCOPES
    // =========================

    // Get chairs that are available
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    // Get chairs that are occupied
    public function scopeOccupied($query)
    {
        return $query->where('status', 'occupied');
    }

    // Get chairs under maintenance
    public function scopeUnderMaintenance($query)
    {
        return $query->where('status', 'maintenance');
    }

    // Search by code, name, or location
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('chair_code', 'like', "%{$search}%")
                ->orWhere('name', 'like', "%{$search}%")
                ->orWhere('location', 'like', "%{$search}%");
        });
    }

    // =========================
    // RELATIONSHIPS
    // =========================

    // All appointments for this chair
    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'chair_id');
    }

    // All treatment sessions for this chair
    public function treatmentSessions()
    {
        return $this->hasMany(TreatmentSession::class, 'chair_id');
    }

    // Current active appointment
    public function currentAppointment()
    {
        return $this->hasOne(Appointment::class, 'chair_id')
            ->whereIn('status', ['checked_in', 'in_progress'])
            ->latest();
    }

    // =========================
    // HELPER METHODS
    // =========================

    // Status labels
    public static function statuses()
    {
        return [
            'available' => 'Available',
            'occupied' => 'Occupied',
            'maintenance' => 'Under Maintenance',
            'cleaning' => 'Cleaning',
            'out_of_service' => 'Out of Service'
        ];
    }

    // Human-readable status
    public function getStatusNameAttribute()
    {
        $statuses = self::statuses();
        return $statuses[$this->status] ?? ucfirst($this->status ?? 'Unknown');
    }

    // Tailwind-friendly badge colors
    public function getStatusColorAttribute()
    {
        return match ($this->status) {
            'available' => 'green',     // Tailwind: bg-green-500
            'occupied' => 'yellow',     // Tailwind: bg-yellow-500
            'maintenance' => 'red',     // Tailwind: bg-red-500
            'cleaning' => 'blue',       // Tailwind: bg-blue-500
            'out_of_service' => 'gray', // Tailwind: bg-gray-500
            default => 'light',
        };
    }

    // Human-readable last used
    public function getFormattedLastUsedAttribute()
    {
        return $this->last_used ? $this->last_used->diffForHumans() : 'Never';
    }

    // Quick boolean checks
    public function getIsAvailableAttribute()
    {
        return $this->status === 'available';
    }

    public function getIsOccupiedAttribute()
    {
        return $this->status === 'occupied';
    }

    // Current patient & doctor for the active appointment
    public function getCurrentPatientAttribute()
    {
        return $this->currentAppointment?->patient;
    }

    public function getCurrentDoctorAttribute()
    {
        return $this->currentAppointment?->doctor;
    }

    // =========================
    // STATUS UPDATE METHODS
    // =========================

    // Mark chair as occupied
    public function markAsOccupied()
    {
        $this->update([
            'status' => 'occupied',
            'last_used' => now()
        ]);
    }

    // Mark chair as available
    public function markAsAvailable()
    {
        $this->update([
            'status' => 'available',
            'last_used' => now()
        ]);
    }

    // Mark chair under maintenance
    public function markAsMaintenance($notes = null)
    {
        $this->update([
            'status' => 'maintenance',
            'notes' => $notes ?? $this->notes
        ]);
    }
}
