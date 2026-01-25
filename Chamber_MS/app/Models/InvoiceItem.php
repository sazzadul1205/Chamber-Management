<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    use HasFactory;

    // Mass assignable fields
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

    // Cast types for decimals and integers
    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'discount' => 'decimal:2',
        'tax_percent' => 'decimal:2',
        'total_amount' => 'decimal:2'
    ];

    /*=====================================
     | Relationships
     *=====================================*/

    // Belongs to Invoice
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    // Optional relationships to different item types
    public function procedure()
    {
        return $this->belongsTo(ProcedureCatalog::class, 'item_id');
    }

    public function medicine()
    {
        return $this->belongsTo(Medicine::class, 'item_id');
    }

    public function inventoryItem()
    {
        return $this->belongsTo(InventoryItem::class, 'item_id');
    }

    /*=====================================
     | Accessors / Presentation
     *=====================================*/

    // Tailwind-ready badge for item type
    public function getItemTypeBadgeAttribute()
    {
        $badges = [
            'procedure' => 'px-2 py-1 bg-blue-500 text-white rounded text-xs',
            'medicine' => 'px-2 py-1 bg-green-500 text-white rounded text-xs',
            'inventory' => 'px-2 py-1 bg-teal-500 text-white rounded text-xs',
            'other' => 'px-2 py-1 bg-gray-500 text-white rounded text-xs'
        ];

        return '<span class="' . ($badges[$this->item_type] ?? $badges['other']) . '">' .
            ucfirst($this->item_type) . '</span>';
    }

    // Link to the original item if it exists
    public function getItemLinkAttribute()
    {
        if (!$this->item_id) return null;

        $item = $this->getOriginalItem();
        if (!$item) return null;

        switch ($this->item_type) {
            case 'procedure':
                return '<a href="' . route('procedure_catalog.show', $item->id) . '">' .
                    ($item->procedure_code ?? $this->description) . '</a>';
            case 'medicine':
                return '<a href="' . route('medicines.show', $item->id) . '">' .
                    ($item->medicine_code ?? $this->description) . '</a>';
            case 'inventory':
                return '<a href="' . route('inventory_items.show', $item->id) . '">' .
                    ($item->item_code ?? $this->description) . '</a>';
        }

        return null;
    }

    /*=====================================
     | Methods / Calculations
     *=====================================*/

    // Calculate total including discount and tax
    public function calculateTotal(): float
    {
        $total = ($this->quantity * $this->unit_price) - ($this->discount ?? 0);
        $tax = ($total * ($this->tax_percent ?? 0)) / 100;
        $this->total_amount = $total + $tax;
        return $this->total_amount;
    }

    // Get original related item model
    public function getOriginalItem()
    {
        return match ($this->item_type) {
            'procedure' => $this->procedure,
            'medicine' => $this->medicine,
            'inventory' => $this->inventoryItem,
            default => null,
        };
    }

    // Get displayable item name
    public function getOriginalItemName(): string
    {
        $item = $this->getOriginalItem();

        if ($item) {
            return match ($this->item_type) {
                'procedure' => $item->procedure_name,
                'medicine' => ($item->brand_name ?? '') . ' - ' . ($item->generic_name ?? ''),
                'inventory' => $item->name,
                default => $this->description,
            };
        }

        return $this->description;
    }
}
