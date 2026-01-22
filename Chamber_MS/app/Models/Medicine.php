<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

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
        'status'
    ];

    // =========================
    // SCOPES
    // =========================
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('medicine_code', 'like', "%{$search}%")
                ->orWhere('brand_name', 'like', "%{$search}%")
                ->orWhere('generic_name', 'like', "%{$search}%")
                ->orWhere('manufacturer', 'like', "%{$search}%");
        });
    }

    public function scopeByDosageForm($query, $dosageForm)
    {
        return $query->where('dosage_form', $dosageForm);
    }

    // =========================
    // RELATIONSHIPS (Safe)
    // =========================
    public function prescriptionItems()
    {
        // Only return a real relation if the class and table exist
        if (class_exists(\App\Models\PrescriptionItem::class) && Schema::hasTable('prescription_items')) {

            // Make sure the column exists
            if (Schema::hasColumn('prescription_items', 'medicine_id')) {
                return $this->hasMany(\App\Models\PrescriptionItem::class, 'medicine_id', 'id');
            }

            // Table exists but column missing — return empty relation
            return $this->hasManyDummy();
        }

        // Class or table missing — return empty relation
        return $this->hasManyDummy();
    }


    public function inventoryItems()
    {
        if (
            class_exists(\App\Models\InventoryItem::class)
            && Schema::hasTable('inventory_items')
        ) {
            return $this->hasMany(\App\Models\InventoryItem::class, 'generic_name', 'generic_name');
        }

        return $this->hasManyDummy();
    }

    protected function hasManyDummy()
    {
        // Use a self-reference relation that never returns rows
        return $this->hasMany(static::class, 'id', 'id')->whereRaw('1=0');
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
            'other' => 'Other'
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
            'other' => 'Other'
        ];
    }

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
        try {
            return $this->prescriptionItems()->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    // =========================
    // CATEGORY INFERENCE (Safe)
    // =========================
    public function inferCategory()
    {
        $generic = strtolower($this->generic_name ?? '');

        if (
            str_contains($generic, 'paracetamol') || str_contains($generic, 'ibuprofen') ||
            str_contains($generic, 'diclofenac') || str_contains($generic, 'aceclofenac') ||
            str_contains($generic, 'tramadol')
        ) {
            return 'analgesic';
        } elseif (
            str_contains($generic, 'amoxicillin') || str_contains($generic, 'metronidazole') ||
            str_contains($generic, 'clarithromycin') || str_contains($generic, 'doxycycline') ||
            str_contains($generic, 'cefixime')
        ) {
            return 'antibiotic';
        } elseif (str_contains($generic, 'lidocaine') || str_contains($generic, 'articaine')) {
            return 'local_anesthetic';
        } elseif (
            str_contains($generic, 'chlorhexidine') || str_contains($generic, 'povidone') ||
            str_contains($generic, 'fluoride')
        ) {
            return 'mouthwash';
        } elseif (str_contains($generic, 'gel') || str_contains($generic, 'ointment') || str_contains($generic, 'paste')) {
            return 'topical';
        } elseif (str_contains($generic, 'chlorzoxazone') || str_contains($generic, 'tizanidine')) {
            return 'muscle_relaxant';
        } elseif (str_contains($generic, 'fluconazole') || str_contains($generic, 'nystatin')) {
            return 'antifungal';
        } elseif (
            str_contains($generic, 'dexamethasone') || str_contains($generic, 'prednisolone') ||
            str_contains($generic, 'triamcinolone')
        ) {
            return 'corticosteroid';
        } elseif (
            str_contains($generic, 'pantoprazole') || str_contains($generic, 'omeprazole') ||
            str_contains($generic, 'ranitidine')
        ) {
            return 'gi_medicine';
        } elseif (str_contains($generic, 'epinephrine') || str_contains($generic, 'chlorpheniramine')) {
            return 'emergency';
        } elseif (
            str_contains($generic, 'tranexamic') || str_contains($generic, 'hydrogen') ||
            str_contains($generic, 'saline')
        ) {
            return 'dental_specific';
        }

        return 'other';
    }

    public function getCategoryAttribute()
    {
        return $this->inferCategory();
    }

    public function getCategoryNameAttribute()
    {
        $categories = self::medicineCategories();
        $category = $this->inferCategory();
        return $categories[$category] ?? 'Other';
    }
}
