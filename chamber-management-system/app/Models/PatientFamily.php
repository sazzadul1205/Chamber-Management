<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatientFamily extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'patient_families';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'head_patient_id',
        'family_name',
    ];

    /**
     * Get the head patient (family head).
     */
    public function headPatient()
    {
        return $this->belongsTo(Patient::class, 'head_patient_id');
    }

    /**
     * Get all family members.
     */
    public function members()
    {
        return $this->hasMany(PatientFamilyMember::class, 'family_id');
    }

    /**
     * Get all patients in this family through members.
     */
    public function patients()
    {
        return $this->hasManyThrough(
            Patient::class,
            PatientFamilyMember::class,
            'family_id', // Foreign key on PatientFamilyMember table
            'id', // Foreign key on Patient table
            'id', // Local key on PatientFamily table
            'patient_id' // Local key on PatientFamilyMember table
        );
    }

    /**
     * Count of family members.
     */
    public function getMemberCountAttribute()
    {
        return $this->members()->count();
    }

    /**
     * Scope to search families by name or head patient.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('family_name', 'like', "%{$search}%")
            ->orWhereHas('headPatient', function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                    ->orWhere('patient_code', 'like', "%{$search}%");
            });
    }
}
