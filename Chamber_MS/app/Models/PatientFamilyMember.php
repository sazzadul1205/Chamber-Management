<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatientFamilyMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'family_id',
        'patient_id',
        'relationship',
        'is_head',
    ];

    protected $table = 'patient_family_members';

    // Relationships
    public function family()
    {
        return $this->belongsTo(PatientFamily::class, 'family_id');
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }
}
