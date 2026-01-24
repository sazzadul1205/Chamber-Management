<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatientFamily extends Model
{
    use HasFactory;

    protected $fillable = [
        'family_code',
        'family_name',
        'head_patient_id',
    ];

    protected $table = 'patient_families';

    // Relationships
    public function head()
    {
        return $this->belongsTo(Patient::class, 'head_patient_id');
    }

    public function members()
    {
        return $this->hasMany(PatientFamilyMember::class, 'family_id');
    }

    public function patients()
    {
        return $this->belongsToMany(Patient::class, 'patient_family_members', 'family_id', 'patient_id')
            ->withPivot('relationship', 'is_head')
            ->withTimestamps();
    }

    // Helper Methods
    public function getMemberCountAttribute()
    {
        return $this->members()->count();
    }

    public function getHeadNameAttribute()
    {
        return $this->head->full_name ?? 'N/A';
    }

    public static function generateFamilyCode()
    {
        $last = self::orderByDesc('family_code')->first();
        $next = $last ? ((int) substr($last->family_code, 3)) + 1 : 1;
        return 'FAM' . str_pad($next, 3, '0', STR_PAD_LEFT);
    }
}
