<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryTransaction extends Model
{
    use HasFactory;

    /**
     * Mass-assignable fields
     */
    protected $fillable = [
        'transaction_code',   // Auto-generated reference code (TRN0001)
        'item_id',            // Inventory item reference
        'transaction_type',   // purchase | adjustment | consumption | return | transfer_in | transfer_out
        'quantity',           // Always positive integer
        'unit_price',         // Cost per unit (if applicable)
        'total_amount',       // quantity * unit_price
        'reference_id',       // Polymorphic reference ID
        'reference_type',     // Polymorphic reference model
        'reference_no',       // External reference number
        'notes',              // Human-readable explanation
        'transaction_date',   // When it happened
        'created_by',         // User ID
    ];

    /**
     * Attribute casting
     */
    protected $casts = [
        'quantity'         => 'integer',
        'unit_price'       => 'decimal:2',
        'total_amount'     => 'decimal:2',
        'transaction_date' => 'datetime',
    ];

    /* =====================================================
     |  RELATIONSHIPS
     ===================================================== */

    public function item()
    {
        return $this->belongsTo(InventoryItem::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /* =====================================================
     |  SCOPES — KEEP QUERIES CLEAN
     ===================================================== */

    public function scopePurchases($query)
    {
        return $query->where('transaction_type', 'purchase');
    }

    public function scopeAdjustments($query)
    {
        return $query->where('transaction_type', 'adjustment');
    }

    public function scopeConsumptions($query)
    {
        return $query->where('transaction_type', 'consumption');
    }

    public function scopeReturns($query)
    {
        return $query->where('transaction_type', 'return');
    }

    public function scopeForItem($query, $itemId)
    {
        return $query->where('item_id', $itemId);
    }

    public function scopeBetweenDates($query, $from, $to)
    {
        return $query->whereBetween('transaction_date', [$from, $to]);
    }

    /* =====================================================
     |  ACCESSORS — UI FRIENDLY
     ===================================================== */

    /**
     * Transaction type badge (Bootstrap-compatible)
     * Tailwind version should be handled in Blade
     */
    public function getTransactionTypeBadgeAttribute()
    {
        $map = [
            'purchase'     => ['bg-success', 'Purchase'],
            'adjustment'   => ['bg-info', 'Adjustment'],
            'consumption'  => ['bg-warning', 'Consumption'],
            'return'       => ['bg-danger', 'Return'],
            'transfer_in'  => ['bg-primary', 'Transfer In'],
            'transfer_out' => ['bg-secondary', 'Transfer Out'],
        ];

        [$class, $label] = $map[$this->transaction_type] ?? ['bg-secondary', ucfirst($this->transaction_type)];

        return "<span class=\"badge {$class}\">{$label}</span>";
    }

    /**
     * Quantity with +/- sign based on transaction direction
     */
    public function getQuantityWithSignAttribute()
    {
        return match ($this->transaction_type) {
            'purchase', 'transfer_in', 'return'     => '+' . $this->quantity,
            'consumption', 'transfer_out'           => '-' . $this->quantity,
            default                                   => (string) $this->quantity,
        };
    }

    /**
     * Quantity + unit (e.g. +5 pcs)
     */
    public function getQuantityFormattedAttribute()
    {
        return $this->quantity_with_sign . ' ' . ($this->item->unit ?? 'pcs');
    }

    /* =====================================================
     |  HELPERS
     ===================================================== */

    /**
     * Generate sequential transaction code
     */
    public static function generateTransactionCode()
    {
        $last = self::latest()->value('transaction_code');
        $next = $last ? ((int) substr($last, 3)) + 1 : 1;

        return 'TRN' . str_pad($next, 4, '0', STR_PAD_LEFT);
    }

    /* =====================================================
     |  STOCK SYNCHRONIZATION
     ===================================================== */

    /**
     * Apply this transaction to inventory stock
     *
     * NOTE:
     * - Adjustments DO NOT auto-change stock
     * - Stock must exist before transaction
     */
    public function applyToStock()
    {
        $stock = InventoryStock::where('item_id', $this->item_id)->first();

        if (!$stock) {
            return;
        }

        match ($this->transaction_type) {
            'purchase', 'transfer_in', 'return'
            => $stock->increment('current_stock', $this->quantity),

            'consumption', 'transfer_out'
            => $stock->decrement('current_stock', $this->quantity),

            'adjustment'
            => null, // handled manually
        };

        // Update purchase metadata
        if ($this->transaction_type === 'purchase') {
            $stock->last_purchase_date = $this->transaction_date;
        }

        $stock->update([
            'last_updated' => now(),
            'updated_by'   => $this->created_by,
        ]);
    }
}
