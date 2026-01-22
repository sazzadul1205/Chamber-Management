<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class InventoryItem extends Model
{
    use HasFactory;

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
        // Only apply if inventory_stocks table exists
        if (Schema::hasTable('inventory_stocks')) {
            return $query->whereHas('stock', function ($q) {
                $q->whereRaw('current_stock <= reorder_level');
            });
        }

        // Table missing → return empty result
        return $query->whereRaw('1 = 0');
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
    // RELATIONSHIPS (Safe)
    // =========================
    public function stock()
    {
        if (class_exists(\App\Models\InventoryStock::class) && Schema::hasTable('inventory_stocks')) {
            return $this->hasOne(\App\Models\InventoryStock::class, 'item_id', 'id');
        }

        // Dummy relation to satisfy Eloquent
        return $this->hasOne(static::class, 'id', 'id')->whereRaw('1 = 0');
    }

    public function transactions()
    {
        if (class_exists(\App\Models\InventoryTransaction::class) && Schema::hasTable('inventory_transactions')) {
            return $this->hasMany(\App\Models\InventoryTransaction::class, 'item_id', 'id');
        }

        return $this->hasMany(static::class, 'id', 'id')->whereRaw('1 = 0');
    }

    public function usages()
    {
        if (class_exists(\App\Models\InventoryUsage::class) && Schema::hasTable('inventory_usages')) {
            return $this->hasMany(\App\Models\InventoryUsage::class, 'item_id', 'id');
        }

        return $this->hasMany(static::class, 'id', 'id')->whereRaw('1 = 0');
    }


    protected function hasManyDummy()
    {
        return $this->hasMany(static::class)->whereRaw('1 = 0');
    }

    protected function hasOneDummy()
    {
        return $this->hasOne(static::class)->whereRaw('1 = 0');
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
        if (!Schema::hasTable('inventory_stocks') || !$this->relationLoaded('stock')) {
            return 0;
        }
        return $this->stock ? $this->stock->current_stock : 0;
    }

    public function getStockStatusAttribute()
    {
        if (!Schema::hasTable('inventory_stocks')) {
            return 'unknown';
        }

        $current = $this->current_stock;

        if ($current <= 0) return 'out_of_stock';
        if ($current <= $this->reorder_level) return 'low_stock';
        return 'in_stock';
    }

    public function getStockStatusColorAttribute()
    {
        return match ($this->stock_status) {
            'out_of_stock' => 'danger',
            'low_stock' => 'warning',
            'in_stock' => 'success',
            default => 'secondary',
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
