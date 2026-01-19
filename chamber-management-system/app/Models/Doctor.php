<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Doctor extends Model
{
    use HasFactory;

    /**
     * Mass assignable attributes
     */
    protected $fillable = [
        'user_id',
        'designation',
        'specialization',
        'qualification',
        'experience_years',
        'photo',
        'bio',
        'consultation_fee',
        'commission_percent',
        'is_available',
        'is_featured',
        'slug',
        'display_order',
    ];

    /**
     * Attribute casting
     */
    protected $casts = [
        'consultation_fee'   => 'decimal:2',
        'commission_percent' => 'decimal:2',
        'is_available'       => 'boolean',
        'is_featured'        => 'boolean',
        'experience_years'   => 'integer',
    ];

    /**
     * Relationships
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Accessors (safe & frontend-friendly)
     */
    public function getFullNameAttribute()
    {
        return $this->user->display_name ?? $this->user->name ?? '—';
    }

    public function getPhoneAttribute()
    {
        return $this->user->phone ?? '—';
    }

    public function getEmailAttribute()
    {
        return $this->user->email ?? '—';
    }

    public function getStatusAttribute()
    {
        return $this->user->status ?? 'inactive';
    }

    public function getPhotoUrlAttribute()
    {
        return $this->photo
            ? asset('storage/' . $this->photo)
            : asset('images/default-doctor.png');
    }

    /**
     * Model events
     */
    protected static function booted()
    {
        static::creating(function ($doctor) {
            if (empty($doctor->slug)) {
                $doctor->slug = Str::slug($doctor->full_name . '-' . uniqid());
            }
        });
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->whereHas(
            'user',
            fn($q) =>
            $q->where('status', 'active')
        );
    }

    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->whereHas('user', function ($u) use ($search) {
                $u->where('full_name', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            })
                ->orWhere('specialization', 'like', "%{$search}%")
                ->orWhere('qualification', 'like', "%{$search}%");
        });
    }

    /**
     * Get the appointments for this doctor.
     */
    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    /**
     * Get today's appointments.
     */
    public function todaysAppointments()
    {
        return $this->appointments()
            ->whereDate('appointment_date', today())
            ->orderBy('appointment_time')
            ->get();
    }

    /**
     * Get upcoming appointments.
     */
    public function upcomingAppointments($days = 7)
    {
        return $this->appointments()
            ->whereDate('appointment_date', '>=', today())
            ->whereDate('appointment_date', '<=', today()->addDays($days))
            ->where('status', 'scheduled')
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->get();
    }
}
