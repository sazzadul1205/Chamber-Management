<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DentalChart extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'chart_date',
        'tooth_number',
        'surface',
        'condition',
        'procedure_done',
        'next_checkup',
        'remarks',
        'updated_by',
    ];

    protected $casts = [
        'chart_date' => 'date',
        'next_checkup' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Tooth numbers for adults (Permanent teeth)
    public static function adultTeeth()
    {
        return [
            // Upper Right Quadrant
            '18',
            '17',
            '16',
            '15',
            '14',
            '13',
            '12',
            '11',
            // Upper Left Quadrant
            '21',
            '22',
            '23',
            '24',
            '25',
            '26',
            '27',
            '28',
            // Lower Left Quadrant
            '38',
            '37',
            '36',
            '35',
            '34',
            '33',
            '32',
            '31',
            // Lower Right Quadrant
            '48',
            '47',
            '46',
            '45',
            '44',
            '43',
            '42',
            '41',
        ];
    }

    // Tooth numbers for children (Primary teeth)
    public static function childTeeth()
    {
        return [
            // Upper Right Quadrant
            '55',
            '54',
            '53',
            '52',
            '51',
            // Upper Left Quadrant
            '61',
            '62',
            '63',
            '64',
            '65',
            // Lower Left Quadrant
            '71',
            '72',
            '73',
            '74',
            '75',
            // Lower Right Quadrant
            '85',
            '84',
            '83',
            '82',
            '81',
        ];
    }

    // Tooth surfaces
    public static function surfaces()
    {
        return [
            'occlusal' => 'Occlusal',
            'buccal' => 'Buccal',
            'lingual' => 'Lingual',
            'mesial' => 'Mesial',
            'distal' => 'Distal',
            'incisal' => 'Incisal',
            'palatal' => 'Palatal',
            'labial' => 'Labial',
        ];
    }

    // Common conditions
    public static function conditions()
    {
        return [
            'healthy' => 'Healthy',
            'caries' => 'Caries',
            'filling' => 'Filling',
            'crown' => 'Crown',
            'bridge' => 'Bridge',
            'implant' => 'Implant',
            'missing' => 'Missing',
            'extracted' => 'Extracted',
            'root_canal' => 'Root Canal Treated',
            'fractured' => 'Fractured',
            'discolored' => 'Discolored',
            'hypoplastic' => 'Hypoplastic',
            'impacted' => 'Impacted',
            'partially_erupted' => 'Partially Erupted',
        ];
    }

    // Common procedures
    public static function procedures()
    {
        return [
            'filling' => 'Filling',
            'extraction' => 'Extraction',
            'root_canal' => 'Root Canal Treatment',
            'crown' => 'Crown Placement',
            'bridge' => 'Bridge',
            'implant' => 'Implant',
            'scaling' => 'Scaling',
            'polishing' => 'Polishing',
            'fluoride' => 'Fluoride Treatment',
            'sealant' => 'Sealant',
            'whitening' => 'Whitening',
            'orthodontic' => 'Orthodontic Treatment',
        ];
    }

    // Scopes
    public function scopeForPatient($query, $patientId)
    {
        return $query->where('patient_id', $patientId);
    }

    public function scopeByTooth($query, $toothNumber)
    {
        return $query->where('tooth_number', $toothNumber);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('chart_date', '>=', now()->subDays($days));
    }

    // Helper Methods
    public function getConditionTextAttribute()
    {
        return self::conditions()[$this->condition] ?? $this->condition;
    }

    public function getSurfaceTextAttribute()
    {
        return self::surfaces()[$this->surface] ?? $this->surface;
    }

    public function getToothNameAttribute()
    {
        $toothNames = [
            // Permanent teeth
            '11' => 'Right Maxillary Central Incisor',
            '12' => 'Right Maxillary Lateral Incisor',
            '13' => 'Right Maxillary Canine',
            '14' => 'Right Maxillary First Premolar',
            '15' => 'Right Maxillary Second Premolar',
            '16' => 'Right Maxillary First Molar',
            '17' => 'Right Maxillary Second Molar',
            '18' => 'Right Maxillary Third Molar',
            '21' => 'Left Maxillary Central Incisor',
            '22' => 'Left Maxillary Lateral Incisor',
            '23' => 'Left Maxillary Canine',
            '24' => 'Left Maxillary First Premolar',
            '25' => 'Left Maxillary Second Premolar',
            '26' => 'Left Maxillary First Molar',
            '27' => 'Left Maxillary Second Molar',
            '28' => 'Left Maxillary Third Molar',
            '31' => 'Left Mandibular Central Incisor',
            '32' => 'Left Mandibular Lateral Incisor',
            '33' => 'Left Mandibular Canine',
            '34' => 'Left Mandibular First Premolar',
            '35' => 'Left Mandibular Second Premolar',
            '36' => 'Left Mandibular First Molar',
            '37' => 'Left Mandibular Second Molar',
            '38' => 'Left Mandibular Third Molar',
            '41' => 'Right Mandibular Central Incisor',
            '42' => 'Right Mandibular Lateral Incisor',
            '43' => 'Right Mandibular Canine',
            '44' => 'Right Mandibular First Premolar',
            '45' => 'Right Mandibular Second Premolar',
            '46' => 'Right Mandibular First Molar',
            '47' => 'Right Mandibular Second Molar',
            '48' => 'Right Mandibular Third Molar',
        ];

        return $toothNames[$this->tooth_number] ?? "Tooth {$this->tooth_number}";
    }

    public function getConditionColorAttribute()
    {
        $colors = [
            'healthy' => 'success',
            'caries' => 'danger',
            'filling' => 'info',
            'crown' => 'primary',
            'missing' => 'dark',
            'extracted' => 'secondary',
            'root_canal' => 'warning',
            'fractured' => 'danger',
            'discolored' => 'warning',
        ];

        return $colors[$this->condition] ?? 'secondary';
    }

    public function getConditionBadgeAttribute()
    {
        $color = $this->condition_color;
        return '<span class="badge bg-' . $color . '">' . $this->condition_text . '</span>';
    }
}
