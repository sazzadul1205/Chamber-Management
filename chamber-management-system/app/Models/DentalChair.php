<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DentalChair extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'status' => 'string',
    ];

    /**
     * Get the appointments for this chair.
     * Note: We'll uncomment this after creating Appointments table
     */
    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'chair_id');
    }

    /**
     * Get current appointment (if any).
     */
    public function currentAppointment()
    {
        return $this->appointments()
            ->whereIn('status', ['checked_in', 'in_progress'])
            ->whereDate('appointment_date', today())
            ->latest()
            ->first();
    }

    /**
     * Check if chair is available.
     */
    public function getIsAvailableAttribute()
    {
        return $this->status === 'available';
    }

    /**
     * Check if chair is occupied.
     */
    public function getIsOccupiedAttribute()
    {
        return $this->status === 'occupied';
    }

    /**
     * Check if chair is under maintenance.
     */
    public function getIsUnderMaintenanceAttribute()
    {
        return $this->status === 'maintenance';
    }

    /**
     * Scope for available chairs.
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    /**
     * Scope for occupied chairs.
     */
    public function scopeOccupied($query)
    {
        return $query->where('status', 'occupied');
    }

    /**
     * Scope for maintenance chairs.
     */
    public function scopeMaintenance($query)
    {
        return $query->where('status', 'maintenance');
    }

    /**
     * Update chair status based on current appointments.
     */
    public function updateStatusBasedOnAppointments()
    {
        if ($this->status === 'maintenance') {
            return; // Don't update if under maintenance
        }

        $hasActiveAppointment = $this->appointments()
            ->whereIn('status', ['checked_in', 'in_progress'])
            ->whereDate('appointment_date', today())
            ->exists();

        $this->update([
            'status' => $hasActiveAppointment ? 'occupied' : 'available'
        ]);
    }
}
