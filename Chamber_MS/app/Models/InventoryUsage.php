<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryUsage extends Model
{
    use HasFactory;

    protected $table = 'inventory_usage';

    protected $fillable = [
        'treatment_id',
        'prescription_id',
        'item_id',
        'used_quantity',
        'usage_type',
        'used_by',
        'used_for_patient_id',
        'usage_date',
        'notes'
    ];

    protected $casts = [
        'used_quantity' => 'integer',
        'usage_date' => 'date'
    ];

    // Relationships
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

    // Scopes
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

    // Accessors
    public function getUsageTypeBadgeAttribute()
    {
        $badges = [
            'treatment' => 'badge bg-primary',
            'prescription' => 'badge bg-success',
            'wastage' => 'badge bg-danger',
            'other' => 'badge bg-secondary'
        ];

        $labels = [
            'treatment' => 'Treatment',
            'prescription' => 'Prescription',
            'wastage' => 'Wastage',
            'other' => 'Other'
        ];

        return '<span class="' . ($badges[$this->usage_type] ?? 'badge bg-secondary') . '">' .
            ($labels[$this->usage_type] ?? ucfirst($this->usage_type)) . '</span>';
    }

    public function getReferenceAttribute()
    {
        if ($this->treatment_id) {
            return 'Treatment: ' . ($this->treatment->treatment_code ?? 'N/A');
        } elseif ($this->prescription_id) {
            return 'Prescription: ' . ($this->prescription->prescription_code ?? 'N/A');
        } else {
            return 'Direct Usage';
        }
    }

    public function getReferenceLinkAttribute()
    {
        if ($this->treatment_id && $this->treatment) {
            return '<a href="' . route('treatments.show', $this->treatment_id) . '">' .
                $this->treatment->treatment_code . '</a>';
        } elseif ($this->prescription_id && $this->prescription) {
            return '<a href="' . route('prescriptions.show', $this->prescription_id) . '">' .
                $this->prescription->prescription_code . '</a>';
        } else {
            return 'N/A';
        }
    }

    // Methods
    public function updateInventory()
    {
        // Update inventory stock
        $stock = InventoryStock::where('item_id', $this->item_id)->first();

        if ($stock) {
            $stock->current_stock = max(0, $stock->current_stock - $this->used_quantity);
            $stock->last_updated = now();
            $stock->updated_by = $this->used_by;
            $stock->save();
        }

        // Create inventory transaction
        InventoryTransaction::create([
            'transaction_code' => InventoryTransaction::generateTransactionCode(),
            'item_id' => $this->item_id,
            'transaction_type' => 'consumption',
            'quantity' => $this->used_quantity,
            'reference_id' => $this->id,
            'reference_type' => 'inventory_usage',
            'reference_no' => 'USG-' . $this->id,
            'notes' => $this->notes . ' | Patient: ' . ($this->patient->full_name ?? 'N/A'),
            'transaction_date' => now(),
            'created_by' => $this->used_by
        ]);
    }

    public function revertInventory()
    {
        // Revert inventory stock
        $stock = InventoryStock::where('item_id', $this->item_id)->first();

        if ($stock) {
            $stock->current_stock += $this->used_quantity;
            $stock->last_updated = now();
            $stock->updated_by = $this->used_by;
            $stock->save();
        }

        // Delete associated transaction
        InventoryTransaction::where('reference_id', $this->id)
            ->where('reference_type', 'inventory_usage')
            ->delete();
    }
}
