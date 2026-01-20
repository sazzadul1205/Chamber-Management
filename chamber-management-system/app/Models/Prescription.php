<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prescription extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'treatment_id',
        'created_by',
    ];

    /**
     * Automatically set created_by.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($prescription) {
            if (auth()->check() && empty($prescription->created_by)) {
                $prescription->created_by = auth()->id();
            }
        });
    }

    /**
     * Get the treatment that owns the prescription.
     */
    public function treatment()
    {
        return $this->belongsTo(Treatment::class);
    }

    /**
     * Get the user who created the prescription.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the prescription items.
     */
    public function items()
    {
        return $this->hasMany(PrescriptionItem::class);
    }

    /**
     * Get all medicines through prescription items.
     */
    public function medicines()
    {
        return $this->hasManyThrough(
            Medicine::class,
            PrescriptionItem::class,
            'prescription_id',
            'id',
            'id',
            'medicine_id'
        );
    }
}
