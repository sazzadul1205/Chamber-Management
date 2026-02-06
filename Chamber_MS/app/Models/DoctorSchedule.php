<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DoctorSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'doctor_id',
        'day_of_week',
        'start_time',
        'end_time',
        'is_active',
        'max_appointments',
        'slot_duration',
    ];

    protected $casts = [
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'is_active' => 'boolean',
    ];

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    // Accessors
    public function getFormattedStartTimeAttribute()
    {
        return date('h:i A', strtotime($this->start_time));
    }

    public function getFormattedEndTimeAttribute()
    {
        return date('h:i A', strtotime($this->end_time));
    }

    public function getFormattedDayAttribute()
    {
        return ucfirst($this->day_of_week);
    }

    public function getWorkingHoursAttribute()
    {
        return $this->formatted_start_time . ' - ' . $this->formatted_end_time;
    }

    public function getStatusColorAttribute()
    {
        return $this->is_active ? 'bg-green-500 text-white' : 'bg-gray-300 text-black';
    }

    public function getStatusTextAttribute()
    {
        return $this->is_active ? 'Active' : 'Inactive';
    }
}
