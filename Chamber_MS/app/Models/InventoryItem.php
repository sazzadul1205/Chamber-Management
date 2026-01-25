<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryItem extends Model
{
    use HasFactory;

    // =========================
    // Fillable fields
    // =========================
    protected $fillable = [
        'item_code',
        'name',
        'category',
        'subcategory',
        'unit',
        'description',
        'manufacturer',
        'supplier',
        'reorder_level',
        'optimum_level',
        'status'
    ];

    // =========================
    // Casts
    // =========================
    protected $casts = [
        'reorder_level' => 'integer',
        'optimum_level' => 'integer'
    ];

    // =========================
    // SCOPES
    // =========================
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeLowStock($query)
    {
        return $query->whereHas('stock', function ($q) {
            $q->whereColumn('current_stock', '<=', 'reorder_level');
        });
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeSearch($query, $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('item_code', 'like', "%{$term}%")
                ->orWhere('name', 'like', "%{$term}%")
                ->orWhere('description', 'like', "%{$term}%")
                ->orWhere('manufacturer', 'like', "%{$term}%");
        });
    }

    // =========================
    // RELATIONSHIPS
    // =========================
    public function stock()
    {
        return $this->hasOne(InventoryStock::class, 'item_id');
    }

    public function transactions()
    {
        return $this->hasMany(InventoryTransaction::class, 'item_id');
    }

    public function usages()
    {
        return $this->hasMany(InventoryUsage::class, 'item_id');
    }

    // =========================
    // HELPERS
    // =========================
    public static function categories()
    {
        return [
            'consumable' => 'Consumable',
            'instrument' => 'Instrument',
            'equipment' => 'Equipment',
            'medicine_related' => 'Medicine Related',
            'dental_material' => 'Dental Material',
            'protective_gear' => 'Protective Gear',
            'laboratory' => 'Laboratory',
            'office_supplies' => 'Office Supplies',
            'other' => 'Other'
        ];
    }

    public static function units()
    {
        return [
            'pcs' => 'Pieces',
            'pair' => 'Pair',
            'set' => 'Set',
            'box' => 'Box',
            'pack' => 'Pack',
            'bottle' => 'Bottle',
            'tube' => 'Tube',
            'syringe' => 'Syringe',
            'cartridge' => 'Cartridge',
            'capsule' => 'Capsule',
            'kit' => 'Kit',
            'roll' => 'Roll',
            'meter' => 'Meter',
            'liter' => 'Liter',
            'kg' => 'Kilogram',
            'g' => 'Gram',
            'ml' => 'Milliliter'
        ];
    }

    // =========================
    // Attribute Helpers
    // =========================
    public function getCategoryNameAttribute()
    {
        return self::categories()[$this->category] ?? ucfirst($this->category);
    }

    public function getUnitNameAttribute()
    {
        return self::units()[$this->unit] ?? $this->unit;
    }

    public function getCurrentStockAttribute()
    {
        return $this->stock?->current_stock ?? 0;
    }

    public function getStockStatusAttribute()
    {
        $current = $this->current_stock;

        if ($current <= 0) return 'out_of_stock';
        if ($current <= $this->reorder_level) return 'low_stock';
        return 'in_stock';
    }

    public function getStockStatusColorAttribute()
    {
        return match ($this->stock_status) {
            'out_of_stock' => 'danger',  // Tailwind: red
            'low_stock' => 'warning',   // Tailwind: yellow
            'in_stock' => 'success',    // Tailwind: green
            default => 'secondary',     // Tailwind: gray
        };
    }

    public function getStockStatusTextAttribute()
    {
        return match ($this->stock_status) {
            'out_of_stock' => 'Out of Stock',
            'low_stock' => 'Low Stock',
            'in_stock' => 'In Stock',
            default => 'Unknown',
        };
    }
}
