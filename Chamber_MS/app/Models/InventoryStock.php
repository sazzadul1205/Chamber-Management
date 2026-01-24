<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryStock extends Model
{
    use HasFactory;

    protected $table = 'inventory_stock';

    protected $fillable = [
        'item_id',
        'opening_stock',
        'current_stock',
        'unit_cost',
        'selling_price',
        'last_purchase_date',
        'expiry_date',
        'location',
        'last_updated',
        'updated_by'
    ];

    protected $casts = [
        'opening_stock' => 'integer',
        'current_stock' => 'integer',
        'unit_cost' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'last_purchase_date' => 'date',
        'expiry_date' => 'date',
        'last_updated' => 'datetime'
    ];

    // Relationships
    public function item()
    {
        return $this->belongsTo(InventoryItem::class, 'item_id');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Scopes
    public function scopeLowStock($query)
    {
        return $query->whereRaw('current_stock <= items.reorder_level')
            ->join('inventory_items as items', 'items.id', '=', 'inventory_stock.item_id');
    }

    public function scopeExpiringSoon($query, $days = 30)
    {
        return $query->whereNotNull('expiry_date')
            ->where('expiry_date', '<=', now()->addDays($days))
            ->where('expiry_date', '>', now());
    }

    public function scopeExpired($query)
    {
        return $query->whereNotNull('expiry_date')
            ->where('expiry_date', '<', now());
    }

    public function scopeInStock($query)
    {
        return $query->where('current_stock', '>', 0);
    }

    public function scopeOutOfStock($query)
    {
        return $query->where('current_stock', '<=', 0);
    }

    // Accessors
    public function getStockStatusAttribute()
    {
        if ($this->current_stock <= 0) {
            return 'out_of_stock';
        }

        $reorderLevel = $this->item->reorder_level ?? 10;

        if ($this->current_stock <= $reorderLevel) {
            return 'low_stock';
        }

        return 'in_stock';
    }

    public function getStockStatusBadgeAttribute()
    {
        $status = $this->stock_status;

        $badges = [
            'in_stock' => 'badge bg-success',
            'low_stock' => 'badge bg-warning',
            'out_of_stock' => 'badge bg-danger'
        ];

        $labels = [
            'in_stock' => 'In Stock',
            'low_stock' => 'Low Stock',
            'out_of_stock' => 'Out of Stock'
        ];

        return '<span class="' . ($badges[$status] ?? 'badge bg-secondary') . '">' .
            ($labels[$status] ?? ucfirst($status)) . '</span>';
    }

    public function getDaysToExpiryAttribute()
    {
        if (!$this->expiry_date) {
            return null;
        }

        return now()->diffInDays($this->expiry_date, false);
    }

    public function getExpiryStatusAttribute()
    {
        if (!$this->expiry_date) {
            return 'no_expiry';
        }

        $days = $this->days_to_expiry;

        if ($days < 0) {
            return 'expired';
        } elseif ($days <= 30) {
            return 'expiring_soon';
        } else {
            return 'valid';
        }
    }

    public function getTotalValueAttribute()
    {
        return $this->unit_cost ? $this->current_stock * $this->unit_cost : 0;
    }
}
