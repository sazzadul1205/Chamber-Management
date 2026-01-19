<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DentalChart extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'tooth_number',
        'tooth_condition',
        'remarks',
        'last_updated',
    ];

    protected $casts = [
        'last_updated' => 'datetime',
    ];

    /**
     * Automatically update last_updated timestamp.
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($chart) {
            $chart->last_updated = now();
        });
    }

    /**
     * Tooth belongs to a patient.
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Tooth display name.
     */
    public function getToothDisplayAttribute()
    {
        $toothNames = [
            '11' => 'Upper Right Central Incisor',
            '12' => 'Upper Right Lateral Incisor',
            '13' => 'Upper Right Canine',
            '14' => 'Upper Right First Premolar',
            '15' => 'Upper Right Second Premolar',
            '16' => 'Upper Right First Molar',
            '17' => 'Upper Right Second Molar',
            '18' => 'Upper Right Third Molar',
            '21' => 'Upper Left Central Incisor',
            '22' => 'Upper Left Lateral Incisor',
            '23' => 'Upper Left Canine',
            '24' => 'Upper Left First Premolar',
            '25' => 'Upper Left Second Premolar',
            '26' => 'Upper Left First Molar',
            '27' => 'Upper Left Second Molar',
            '28' => 'Upper Left Third Molar',
            '31' => 'Lower Left Central Incisor',
            '32' => 'Lower Left Lateral Incisor',
            '33' => 'Lower Left Canine',
            '34' => 'Lower Left First Premolar',
            '35' => 'Lower Left Second Premolar',
            '36' => 'Lower Left First Molar',
            '37' => 'Lower Left Second Molar',
            '38' => 'Lower Left Third Molar',
            '41' => 'Lower Right Central Incisor',
            '42' => 'Lower Right Lateral Incisor',
            '43' => 'Lower Right Canine',
            '44' => 'Lower Right First Premolar',
            '45' => 'Lower Right Second Premolar',
            '46' => 'Lower Right First Molar',
            '47' => 'Lower Right Second Molar',
            '48' => 'Lower Right Third Molar',
        ];

        return $toothNames[$this->tooth_number] ?? "Tooth #{$this->tooth_number}";
    }

    /**
     * Condition color for visualization.
     */
    public function getConditionColorAttribute()
    {
        $colors = [
            'healthy' => 'green',
            'cavity' => 'yellow',
            'filled' => 'blue',
            'crown' => 'purple',
            'missing' => 'red',
            'implant' => 'teal',
            'root canal' => 'orange',
            'decay' => 'brown'
        ];

        return $colors[strtolower($this->tooth_condition)] ?? 'gray';
    }

    /**
     * Scope to filter by patient.
     */
    public function scopeForPatient($query, $patientId)
    {
        return $query->where('patient_id', $patientId);
    }

    /**
     * Scope to filter by tooth.
     */
    public function scopeForTooth($query, $toothNumber)
    {
        return $query->where('tooth_number', $toothNumber);
    }
}
