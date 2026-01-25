<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryUsage extends Model
{
    use HasFactory;

    // ================================
    // Mass-assignable fields
    // ================================
    protected $fillable = [
        'treatment_id',
        'prescription_id',
        'item_id',
        'used_quantity',
        'usage_type',
        'used_by',
        'used_for_patient_id',
        'usage_date',
        'notes',
    ];

    // ================================
    // Attribute casting
    // ================================
    protected $casts = [
        'used_quantity' => 'integer',
        'usage_date' => 'date',
    ];

    // ================================
    // Relationships
    // ================================
    public function treatment()
    {
        return $this->belongsTo(Treatment::class);
    }

    public function prescription()
    {
        return $this->belongsTo(Prescription::class);
    }

    public function item()
    {
        return $this->belongsTo(InventoryItem::class, 'item_id');
    }

    public function usedBy()
    {
        return $this->belongsTo(User::class, 'used_by');
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'used_for_patient_id');
    }

    // ================================
    // Query Scopes
    // ================================
    public function scopeForTreatment($query, $treatmentId)
    {
        return $query->where('treatment_id', $treatmentId);
    }

    public function scopeForPrescription($query, $prescriptionId)
    {
        return $query->where('prescription_id', $prescriptionId);
    }

    public function scopeForPatient($query, $patientId)
    {
        return $query->where('used_for_patient_id', $patientId);
    }

    public function scopeForItem($query, $itemId)
    {
        return $query->where('item_id', $itemId);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('usage_type', $type);
    }

    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('usage_date', [$startDate, $endDate]);
    }

    public function scopeRecent($query, $limit = 50)
    {
        return $query->latest()->limit($limit);
    }

    // ================================
    // Accessors
    // ================================
    public function getUsageTypeBadgeAttribute()
    {
        // Tailwind badge colors
        $map = [
            'treatment'    => ['bg-blue-500', 'Treatment'],
            'prescription' => ['bg-green-500', 'Prescription'],
            'wastage'      => ['bg-red-500', 'Wastage'],
            'other'        => ['bg-gray-500', 'Other'],
        ];

        [$class, $label] = $map[$this->usage_type] ?? ['bg-gray-500', ucfirst($this->usage_type)];

        return "<span class=\"px-2 py-1 text-white rounded {$class}\">{$label}</span>";
    }

    public function getReferenceAttribute()
    {
        if ($this->treatment_id) return 'Treatment: ' . ($this->treatment->treatment_code ?? 'N/A');
        if ($this->prescription_id) return 'Prescription: ' . ($this->prescription->prescription_code ?? 'N/A');
        return 'Direct Usage';
    }

    public function getReferenceLinkAttribute()
    {
        if ($this->treatment_id) {
            return '<a href="' . route('treatments.show', $this->treatment_id) . '">' .
                ($this->treatment->treatment_code ?? 'N/A') . '</a>';
        }
        if ($this->prescription_id) {
            return '<a href="' . route('prescriptions.show', $this->prescription_id) . '">' .
                ($this->prescription->prescription_code ?? 'N/A') . '</a>';
        }
        return 'N/A';
    }

    // ================================
    // Inventory Methods
    // ================================
    public function updateInventory()
    {
        // Reduce stock
        InventoryStock::where('item_id', $this->item_id)
            ->first()?->decrement('current_stock', $this->used_quantity);

        // Record transaction
        InventoryTransaction::create([
            'transaction_code' => InventoryTransaction::generateTransactionCode(),
            'item_id'          => $this->item_id,
            'transaction_type' => 'consumption',
            'quantity'         => $this->used_quantity,
            'reference_id'     => $this->id,
            'reference_type'   => 'inventory_usage',
            'reference_no'     => 'USG-' . $this->id,
            'notes'            => $this->notes . ' | Patient: ' . ($this->patient->full_name ?? 'N/A'),
            'transaction_date' => now(),
            'created_by'       => $this->used_by,
        ]);
    }

    public function revertInventory()
    {
        // Revert stock
        InventoryStock::where('item_id', $this->item_id)
            ->first()?->increment('current_stock', $this->used_quantity);

        // Remove transaction
        InventoryTransaction::where('reference_id', $this->id)
            ->where('reference_type', 'inventory_usage')
            ->delete();
    }
}
