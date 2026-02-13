<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DentalChart extends Model
{
    use HasFactory;

    // =========================
    // MASS ASSIGNABLE ATTRIBUTES
    // =========================
    protected $fillable = [
        'patient_id',       // Patient reference
        'chart_date',       // Date of chart entry
        'tooth_number',     // Tooth number (e.g., 11, 12)
        'surface',          // Tooth surface
        'condition',        // Tooth condition
        'procedure_done',   // Procedure performed
        'next_checkup',     // Next checkup date
        'remarks',          // Any notes
        'updated_by',       // User ID who last updated
    ];

    // =========================
    // ATTRIBUTE CASTS
    // =========================
    protected $casts = [
        'chart_date' => 'date',
        'next_checkup' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // =========================
    // RELATIONSHIPS
    // =========================

    /** Patient associated with this chart */
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    /** User who last updated this chart */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // =========================
    // TOOTH DATA
    // =========================

    /** Permanent teeth (adult) */
    public static function adultTeeth(): array
    {
        return [
            '18',
            '17',
            '16',
            '15',
            '14',
            '13',
            '12',
            '11',
            '21',
            '22',
            '23',
            '24',
            '25',
            '26',
            '27',
            '28',
            '38',
            '37',
            '36',
            '35',
            '34',
            '33',
            '32',
            '31',
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

    /** Primary teeth (children) */
    public static function childTeeth(): array
    {
        return [
            '55',
            '54',
            '53',
            '52',
            '51',
            '61',
            '62',
            '63',
            '64',
            '65',
            '71',
            '72',
            '73',
            '74',
            '75',
            '85',
            '84',
            '83',
            '82',
            '81',
        ];
    }

    /** Tooth surfaces */
    public static function surfaces(): array
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

    /** Tooth conditions */
    public static function conditions(): array
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

    /** Procedures performed */
    public static function procedures(): array
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

    // =========================
    // SCOPES
    // =========================

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

    // =========================
    // ACCESSORS
    // =========================

    /** Get readable condition */
    public function getConditionTextAttribute(): string
    {
        return self::conditions()[$this->condition] ?? $this->condition;
    }

    /** Get readable surface */
    public function getSurfaceTextAttribute(): string
    {
        return self::surfaces()[$this->surface] ?? $this->surface;
    }

    /** Get full tooth name */
    public static function toothNames(): array
    {
        return [
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
            '51' => 'Right Maxillary Primary Central Incisor',
            '52' => 'Right Maxillary Primary Lateral Incisor',
            '53' => 'Right Maxillary Primary Canine',
            '54' => 'Right Maxillary Primary First Molar',
            '55' => 'Right Maxillary Primary Second Molar',
            '61' => 'Left Maxillary Primary Central Incisor',
            '62' => 'Left Maxillary Primary Lateral Incisor',
            '63' => 'Left Maxillary Primary Canine',
            '64' => 'Left Maxillary Primary First Molar',
            '65' => 'Left Maxillary Primary Second Molar',
            '71' => 'Left Mandibular Primary Central Incisor',
            '72' => 'Left Mandibular Primary Lateral Incisor',
            '73' => 'Left Mandibular Primary Canine',
            '74' => 'Left Mandibular Primary First Molar',
            '75' => 'Left Mandibular Primary Second Molar',
            '81' => 'Right Mandibular Primary Central Incisor',
            '82' => 'Right Mandibular Primary Lateral Incisor',
            '83' => 'Right Mandibular Primary Canine',
            '84' => 'Right Mandibular Primary First Molar',
            '85' => 'Right Mandibular Primary Second Molar',
        ];
    }

    /** Get full tooth name */
    public function getToothNameAttribute(): string
    {
        $names = self::toothNames();
        return $names[$this->tooth_number] ?? "Tooth {$this->tooth_number}";
    }

    /** Tailwind badge color for condition */
    public function getConditionColorAttribute(): string
    {
        $colors = [
            'healthy' => 'green',
            'caries' => 'red',
            'filling' => 'blue',
            'crown' => 'purple',
            'missing' => 'gray',
            'extracted' => 'gray',
            'root_canal' => 'yellow',
            'fractured' => 'red',
            'discolored' => 'yellow',
        ];

        return $colors[$this->condition] ?? 'gray';
    }

    /** Tailwind badge HTML for Blade */
    public function getConditionBadgeAttribute(): string
    {
        $color = $this->condition_color;
        $text = $this->condition_text;
        return "<span class='px-2 py-1 rounded-full text-white bg-{$color}-500 text-xs'>{$text}</span>";
    }
}
