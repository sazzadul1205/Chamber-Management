<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'item_type',
        'item_id',
        'description',
        'quantity',
        'unit_price',
        'discount',
        'tax_percent',
        'total_amount'
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'discount' => 'decimal:2',
        'tax_percent' => 'decimal:2',
        'total_amount' => 'decimal:2'
    ];

    // Relationships
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function procedure()
    {
        return $this->belongsTo(ProcedureCatalog::class, 'item_id')
            ->where('item_type', 'procedure');
    }

    public function medicine()
    {
        return $this->belongsTo(Medicine::class, 'item_id')
            ->where('item_type', 'medicine');
    }

    public function inventoryItem()
    {
        return $this->belongsTo(InventoryItem::class, 'item_id')
            ->where('item_type', 'inventory');
    }

    // Accessors
    public function getItemTypeBadgeAttribute()
    {
        $badges = [
            'procedure' => 'badge bg-primary',
            'medicine' => 'badge bg-success',
            'inventory' => 'badge bg-info',
            'other' => 'badge bg-secondary'
        ];

        return '<span class="' . ($badges[$this->item_type] ?? 'badge bg-secondary') . '">' .
            ucfirst($this->item_type) . '</span>';
    }

    public function getItemLinkAttribute()
    {
        if (!$this->item_id) {
            return null;
        }

        switch ($this->item_type) {
            case 'procedure':
                $item = $this->procedure;
                if ($item) {
                    return '<a href="' . route('procedure_catalog.show', $item->id) . '">' .
                        $item->procedure_code . '</a>';
                }
                break;
            case 'medicine':
                $item = $this->medicine;
                if ($item) {
                    return '<a href="' . route('medicines.show', $item->id) . '">' .
                        $item->medicine_code . '</a>';
                }
                break;
            case 'inventory':
                $item = $this->inventoryItem;
                if ($item) {
                    return '<a href="' . route('inventory_items.show', $item->id) . '">' .
                        $item->item_code . '</a>';
                }
                break;
        }

        return null;
    }

    // Methods
    public function calculateTotal()
    {
        $total = ($this->quantity * $this->unit_price) - $this->discount;
        $tax = ($total * $this->tax_percent) / 100;
        $this->total_amount = $total + $tax;
        return $this->total_amount;
    }

    public function getOriginalItem()
    {
        switch ($this->item_type) {
            case 'procedure':
                return $this->procedure;
            case 'medicine':
                return $this->medicine;
            case 'inventory':
                return $this->inventoryItem;
            default:
                return null;
        }
    }

    public function getOriginalItemName()
    {
        $item = $this->getOriginalItem();
        if ($item) {
            if ($this->item_type == 'procedure') {
                return $item->procedure_name;
            } elseif ($this->item_type == 'medicine') {
                return $item->brand_name . ' - ' . $item->generic_name;
            } elseif ($this->item_type == 'inventory') {
                return $item->name;
            }
        }

        return $this->description;
    }
}
