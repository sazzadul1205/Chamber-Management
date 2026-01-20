<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TreatmentProcedure extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'treatment_id',
        'procedure_name',
        'tooth_no',
        'price',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'decimal:2',
    ];

    /**
     * Get the treatment that owns the procedure.
     */
    public function treatment()
    {
        return $this->belongsTo(Treatment::class);
    }

    /**
     * Check if procedure is completed.
     */
    public function getIsCompletedAttribute()
    {
        return $this->status === 'done';
    }

    /**
     * Get status color for display.
     */
    public function getStatusColorAttribute()
    {
        return $this->status === 'done' ? 'success' : 'warning';
    }

    /**
     * Scope for completed procedures.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'done');
    }

    /**
     * Scope for planned procedures.
     */
    public function scopePlanned($query)
    {
        return $query->where('status', 'planned');
    }

    /**
     * Scope for procedures by tooth number.
     */
    public function scopeByTooth($query, $toothNo)
    {
        return $query->where('tooth_no', $toothNo);
    }
}
