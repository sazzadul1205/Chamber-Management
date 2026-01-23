<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class DentalChair extends Model
{
    use HasFactory;

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
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    public function scopeOccupied($query)
    {
        return $query->where('status', 'occupied');
    }

    public function scopeUnderMaintenance($query)
    {
        return $query->where('status', 'maintenance');
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('chair_code', 'like', "%{$search}%")
                ->orWhere('name', 'like', "%{$search}%")
                ->orWhere('location', 'like', "%{$search}%");
        });
    }

    // =========================
    // RELATIONSHIPS WITH FALLBACKS
    // =========================
    public function appointments()
    {
        if (class_exists(Appointment::class) && Schema::hasTable('appointments')) {
            return $this->hasMany(Appointment::class, 'chair_id');
        }
        return $this->hasManyDummy(); // returns empty collection, no SQL error
    }


    public function treatmentSessions()
    {
        if (class_exists(TreatmentSession::class) && Schema::hasTable('treatment_sessions')) {
            return $this->hasMany(TreatmentSession::class, 'chair_id');
        }
        return $this->hasManyDummy();
    }

    public function currentAppointment()
    {
        if (class_exists(Appointment::class) && Schema::hasTable('appointments')) {
            return $this->hasOne(Appointment::class, 'chair_id')
                ->whereIn('status', ['checked_in', 'in_progress'])
                ->latest();
        }

        // Return a dummy hasOne relationship to avoid errors
        return $this->hasOne(DentalChair::class, 'id', 'id')->whereRaw('1=0');
    }


    protected function hasManyDummy()
    {
        // Return empty collection for fallback
        return $this->hasMany(DentalChair::class, 'id', 'id')->whereRaw('1=0');
    }

    // =========================
    // HELPER METHODS
    // =========================
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

    public function getStatusNameAttribute()
    {
        $statuses = self::statuses();
        return $statuses[$this->status] ?? ucfirst($this->status ?? 'Unknown');
    }

    public function getStatusColorAttribute()
    {
        return match ($this->status) {
            'available' => 'success',
            'occupied' => 'warning',
            'maintenance' => 'danger',
            'cleaning' => 'info',
            'out_of_service' => 'secondary',
            default => 'light'
        };
    }

    public function getFormattedLastUsedAttribute()
    {
        if (!$this->last_used) {
            return 'Never';
        }

        $diff = now()->diffInMinutes($this->last_used);

        if ($diff < 60) {
            return $diff . ' minutes ago';
        } elseif ($diff < 1440) {
            $hours = floor($diff / 60);
            return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
        } else {
            return $this->last_used->format('d M Y, h:i A');
        }
    }

    public function getIsAvailableAttribute()
    {
        return $this->status === 'available';
    }

    public function getIsOccupiedAttribute()
    {
        return $this->status === 'occupied';
    }

    public function getCurrentPatientAttribute()
    {
        $appointment = $this->currentAppointment;
        return $appointment?->patient ?? null;
    }

    public function getCurrentDoctorAttribute()
    {
        $appointment = $this->currentAppointment;
        return $appointment?->doctor ?? null;
    }

    // =========================
    // STATUS UPDATES
    // =========================
    public function markAsOccupied()
    {
        $this->updateSafe([
            'status' => 'occupied',
            'last_used' => now()
        ]);
    }

    public function markAsAvailable()
    {
        $this->updateSafe([
            'status' => 'available',
            'last_used' => now()
        ]);
    }

    public function markAsMaintenance($notes = null)
    {
        $this->updateSafe([
            'status' => 'maintenance',
            'notes' => $notes ?? $this->notes
        ]);
    }

    // Safe update wrapper
    protected function updateSafe(array $data)
    {
        try {
            $this->update($data);
        } catch (\Exception $e) {
            // silently fail if DB is unavailable
        }
    }
}
