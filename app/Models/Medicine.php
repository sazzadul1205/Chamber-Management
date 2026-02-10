<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medicine extends Model
{
    use HasFactory;

    protected $fillable = [
        'medicine_code',
        'brand_name',
        'generic_name',
        'strength',
        'dosage_form',
        'unit',
        'manufacturer',
        'status',
    ];

    // =========================
    // SCOPES
    // =========================
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeSearch($query, $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('medicine_code', 'like', "%{$term}%")
                ->orWhere('brand_name', 'like', "%{$term}%")
                ->orWhere('generic_name', 'like', "%{$term}%")
                ->orWhere('manufacturer', 'like', "%{$term}%");
        });
    }

    public function scopeByDosageForm($query, $dosageForm)
    {
        return $query->where('dosage_form', $dosageForm);
    }

    // =========================
    // RELATIONSHIPS
    // =========================
    public function prescriptionItems()
    {
        return $this->hasMany(\App\Models\PrescriptionItem::class, 'medicine_id', 'id');
    }

    public function inventoryItems()
    {
        return $this->hasMany(\App\Models\InventoryItem::class, 'generic_name', 'generic_name');
    }

    // =========================
    // HELPERS
    // =========================
    public static function dosageForms()
    {
        return [
            'tablet' => 'Tablet',
            'capsule' => 'Capsule',
            'syrup' => 'Syrup',
            'injection' => 'Injection',
            'gel' => 'Gel',
            'ointment' => 'Ointment',
            'mouthwash' => 'Mouthwash',
            'spray' => 'Spray',
            'drops' => 'Drops',
            'powder' => 'Powder',
            'cream' => 'Cream',
            'other' => 'Other',
        ];
    }

    public static function medicineCategories()
    {
        return [
            'analgesic' => 'Analgesic (Pain Killer)',
            'antibiotic' => 'Antibiotic',
            'local_anesthetic' => 'Local Anesthetic',
            'mouthwash' => 'Mouthwash/Rinse',
            'topical' => 'Topical Application',
            'muscle_relaxant' => 'Muscle Relaxant',
            'antifungal' => 'Antifungal',
            'corticosteroid' => 'Corticosteroid',
            'gi_medicine' => 'GI Medicine',
            'emergency' => 'Emergency Medicine',
            'dental_specific' => 'Dental Specific',
            'other' => 'Other',
        ];
    }

    // =========================
    // ATTRIBUTES
    // =========================
    public function getDosageFormNameAttribute()
    {
        $forms = self::dosageForms();
        return $forms[$this->dosage_form] ?? ucfirst($this->dosage_form ?? 'Unknown');
    }

    public function getFullNameAttribute()
    {
        $brand = $this->brand_name ?? '';
        $generic = $this->generic_name ?? '';
        $strength = $this->strength ?? '';
        return trim("{$brand} ({$generic}) {$strength}");
    }

    public function getFormattedCodeAttribute()
    {
        return '<span class="badge bg-info">' . ($this->medicine_code ?? '-') . '</span>';
    }

    public function getUsageCountAttribute()
    {
        return $this->prescriptionItems()->count();
    }

    // =========================
    // CATEGORY INFERENCE
    // =========================
    public function inferCategory(): string
    {
        $generic = strtolower($this->generic_name ?? '');
        $genericWords = explode(' ', $generic);

        $analgesics = ['paracetamol', 'ibuprofen', 'diclofenac', 'aceclofenac', 'tramadol', 'naproxen'];
        $antibiotics = ['amoxicillin', 'metronidazole', 'clarithromycin', 'doxycycline', 'cefixime', 'azithromycin'];
        $anesthetics = ['lidocaine', 'articaine', 'mepivacaine', 'bupivacaine'];
        $mouthwash = ['chlorhexidine', 'povidone', 'fluoride', 'hexetidine'];
        $topicals = ['gel', 'ointment', 'paste', 'cream', 'lotion'];
        $muscleRelaxants = ['chlorzoxazone', 'tizanidine', 'methocarbamol'];
        $antifungals = ['fluconazole', 'nystatin', 'clotrimazole'];
        $corticosteroids = ['dexamethasone', 'prednisolone', 'triamcinolone', 'hydrocortisone'];
        $giMedicines = ['pantoprazole', 'omeprazole', 'ranitidine', 'domperidone'];
        $emergency = ['epinephrine', 'adrenaline', 'chlorpheniramine'];
        $dental = ['tranexamic', 'hydrogen', 'saline', 'iodine', 'formocresol'];

        // Check each category
        foreach (
            [
                'analgesic' => $analgesics,
                'antibiotic' => $antibiotics,
                'local_anesthetic' => $anesthetics,
                'mouthwash' => $mouthwash,
                'topical' => $topicals,
                'muscle_relaxant' => $muscleRelaxants,
                'antifungal' => $antifungals,
                'corticosteroid' => $corticosteroids,
                'gi_medicine' => $giMedicines,
                'emergency' => $emergency,
                'dental_specific' => $dental,
            ] as $category => $keywords
        ) {
            foreach ($keywords as $keyword) {
                if (str_contains($generic, $keyword)) {
                    return $category;
                }
            }
        }

        return 'other';
    }

    public function getCategoryAttribute(): string
    {
        return $this->inferCategory();
    }

    public function getCategoryNameAttribute(): string
    {
        $categories = self::medicineCategories();
        $category = $this->inferCategory();
        return $categories[$category] ?? 'Other';
    }
}
