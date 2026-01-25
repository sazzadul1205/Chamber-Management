<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatientFamily extends Model
{
    use HasFactory;

    protected $table = 'patient_families';

    protected $fillable = [
        'family_code',
        'family_name',
        'head_patient_id',
    ];

    // =========================
    // RELATIONSHIPS
    // =========================

    /**
     * Head of the family (one patient)
     */
    public function head()
    {
        return $this->belongsTo(Patient::class, 'head_patient_id');
    }

    /**
     * Family members via PatientFamilyMember
     */
    public function members()
    {
        return $this->hasMany(PatientFamilyMember::class, 'family_id');
    }

    /**
     * Patients in the family (many-to-many with extra pivot)
     */
    public function patients()
    {
        return $this->belongsToMany(Patient::class, 'patient_family_members', 'family_id', 'patient_id')
            ->withPivot('relationship', 'is_head')
            ->withTimestamps();
    }

    // =========================
    // HELPER METHODS
    // =========================

    /**
     * Total number of members in the family
     */
    public function getMemberCountAttribute(): int
    {
        return $this->members()->count();
    }

    /**
     * Head's full name
     */
    public function getHeadNameAttribute(): string
    {
        return $this->head->full_name ?? 'N/A';
    }

    /**
     * Generate next family code (FAM001, FAM002, etc.)
     */
    public static function generateFamilyCode(): string
    {
        $last = self::orderByDesc('family_code')->first();
        $next = 1;

        if ($last && preg_match('/FAM(\d+)/', $last->family_code, $matches)) {
            $next = intval($matches[1]) + 1;
        }

        return 'FAM' . str_pad($next, 3, '0', STR_PAD_LEFT);
    }
}
