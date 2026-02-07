<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryStock extends Model
{
    use HasFactory;

    protected $table = 'inventory_stock';

    /**
     * Mass assignable attributes
     */
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
        'updated_by',
    ];

    /**
     * Attribute casting
     */
    protected $casts = [
        'opening_stock'     => 'integer',
        'current_stock'     => 'integer',
        'unit_cost'         => 'decimal:2',
        'selling_price'     => 'decimal:2',
        'last_purchase_date'=> 'date',
        'expiry_date'       => 'date',
        'last_updated'      => 'datetime',
    ];

    // ==================================================
    // RELATIONSHIPS
    // ==================================================

    public function item()
    {
        return $this->belongsTo(InventoryItem::class, 'item_id');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // ==================================================
    // QUERY SCOPES
    // ==================================================

    /**
     * Items below or equal to reorder level
     */
    public function scopeLowStock($query)
    {
        return $query
            ->join('inventory_items as items', 'items.id', '=', 'inventory_stock.item_id')
            ->whereColumn('inventory_stock.current_stock', '<=', 'items.reorder_level')
            ->select('inventory_stock.*');
    }

    /**
     * Items expiring within given days
     */
    public function scopeExpiringSoon($query, int $days = 30)
    {
        return $query
            ->whereNotNull('expiry_date')
            ->whereBetween('expiry_date', [now(), now()->addDays($days)]);
    }

    /**
     * Expired items
     */
    public function scopeExpired($query)
    {
        return $query
            ->whereNotNull('expiry_date')
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

    // ==================================================
    // STOCK STATUS DEFINITIONS
    // ==================================================

    public static function stockStatuses(): array
    {
        return [
            'in_stock'     => ['label' => 'In Stock',     'color' => 'green'],
            'low_stock'    => ['label' => 'Low Stock',    'color' => 'amber'],
            'out_of_stock' => ['label' => 'Out of Stock', 'color' => 'red'],
        ];
    }

    // ==================================================
    // ACCESSORS
    // ==================================================

    /**
     * Logical stock status
     */
    public function getStockStatusAttribute(): string
    {
        if ($this->current_stock <= 0) {
            return 'out_of_stock';
        }

        $reorderLevel = $this->item?->reorder_level ?? 10;

        if ($this->current_stock <= $reorderLevel) {
            return 'low_stock';
        }

        return 'in_stock';
    }

    /**
     * Stock status label (UI-safe)
     */
    public function getStockStatusLabelAttribute(): string
    {
        return self::stockStatuses()[$this->stock_status]['label']
            ?? ucfirst(str_replace('_', ' ', $this->stock_status));
    }

    /**
     * Tailwind color keyword
     */
    public function getStockStatusColorAttribute(): string
    {
        return self::stockStatuses()[$this->stock_status]['color']
            ?? 'gray';
    }

    /**
     * Days remaining until expiry (negative = expired)
     */
    public function getDaysToExpiryAttribute(): ?int
    {
        return $this->expiry_date
            ? now()->diffInDays($this->expiry_date, false)
            : null;
    }

    /**
     * Expiry logical status
     */
    public function getExpiryStatusAttribute(): string
    {
        if (!$this->expiry_date) {
            return 'no_expiry';
        }

        return match (true) {
            $this->days_to_expiry < 0   => 'expired',
            $this->days_to_expiry <= 30 => 'expiring_soon',
            default                     => 'valid',
        };
    }

    /**
     * Total stock value
     */
    public function getTotalValueAttribute(): float
    {
        return round($this->current_stock * ($this->unit_cost ?? 0), 2);
    }
}
