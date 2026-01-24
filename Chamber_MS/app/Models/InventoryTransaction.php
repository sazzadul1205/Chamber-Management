<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_code',
        'item_id',
        'transaction_type',
        'quantity',
        'unit_price',
        'total_amount',
        'reference_id',
        'reference_type',
        'reference_no',
        'notes',
        'transaction_date',
        'created_by'
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'transaction_date' => 'datetime'
    ];

    // Relationships
    public function item()
    {
        return $this->belongsTo(InventoryItem::class, 'item_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Scopes
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

    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('transaction_date', [$startDate, $endDate]);
    }

    // Accessors
    public function getTransactionTypeBadgeAttribute()
    {
        $badges = [
            'purchase' => 'badge bg-success',
            'adjustment' => 'badge bg-info',
            'consumption' => 'badge bg-warning',
            'return' => 'badge bg-danger',
            'transfer_in' => 'badge bg-primary',
            'transfer_out' => 'badge bg-secondary'
        ];

        $labels = [
            'purchase' => 'Purchase',
            'adjustment' => 'Adjustment',
            'consumption' => 'Consumption',
            'return' => 'Return',
            'transfer_in' => 'Transfer In',
            'transfer_out' => 'Transfer Out'
        ];

        return '<span class="' . ($badges[$this->transaction_type] ?? 'badge bg-secondary') . '">' .
            ($labels[$this->transaction_type] ?? ucfirst($this->transaction_type)) . '</span>';
    }

    public function getQuantityWithSignAttribute()
    {
        $sign = '';
        switch ($this->transaction_type) {
            case 'purchase':
            case 'transfer_in':
            case 'return':
                $sign = '+';
                break;
            case 'consumption':
            case 'transfer_out':
                $sign = '-';
                break;
        }

        return $sign . $this->quantity;
    }

    public function getQuantityFormattedAttribute()
    {
        return $this->quantity_with_sign . ' ' . ($this->item->unit ?? 'pcs');
    }

    public static function generateTransactionCode()
    {
        $latest = self::latest()->first();
        $number = $latest ? intval(substr($latest->transaction_code, 3)) + 1 : 1;
        return 'TRN' . str_pad($number, 4, '0', STR_PAD_LEFT);
    }

    // Methods
    public function updateStock()
    {
        $stock = InventoryStock::where('item_id', $this->item_id)->first();

        if ($stock) {
            switch ($this->transaction_type) {
                case 'purchase':
                case 'transfer_in':
                case 'return':
                    $stock->current_stock += $this->quantity;
                    break;
                case 'consumption':
                case 'transfer_out':
                    $stock->current_stock = max(0, $stock->current_stock - $this->quantity);
                    break;
                case 'adjustment':
                    // For adjustments, we don't auto-update stock
                    // Stock is updated manually in adjustment form
                    break;
            }

            // Update last purchase date for purchases
            if ($this->transaction_type === 'purchase') {
                $stock->last_purchase_date = $this->transaction_date;
            }

            $stock->last_updated = now();
            $stock->updated_by = $this->created_by;
            $stock->save();
        }
    }
}
