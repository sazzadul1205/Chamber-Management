<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatientFamilyMember extends Model
{
    use HasFactory;

    // =========================
    // MASS ASSIGNABLE ATTRIBUTES
    // =========================
    protected $fillable = [
        'family_id',     // Reference to the PatientFamily
        'patient_id',    // Reference to the Patient
        'relationship',  // Relationship to the family head (e.g., spouse, child)
        'is_head',       // Boolean flag to indicate if this member is the head
    ];

    // Explicit table name (optional if it matches convention)
    protected $table = 'patient_family_members';

    // =========================
    // RELATIONSHIPS
    // =========================

    /**
     * Get the family this member belongs to.
     */
    public function family()
    {
        return $this->belongsTo(PatientFamily::class, 'family_id');
    }

    /**
     * Get the patient associated with this family member.
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    // =========================
    // ACCESSORS (optional)
    // =========================

    /**
     * Get a readable status for the head.
     */
    public function getHeadLabelAttribute()
    {
        return $this->is_head ? 'Head' : 'Member';
    }

    /**
     * Display a friendly relationship label.
     */
    public function getRelationshipLabelAttribute()
    {
        return ucfirst($this->relationship ?? 'N/A');
    }
}
