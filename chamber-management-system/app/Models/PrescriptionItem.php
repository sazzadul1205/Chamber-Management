<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrescriptionItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'prescription_id',
        'medicine_id',
        'dosage',
        'duration',
    ];

    /**
     * Get the prescription that owns the item.
     */
    public function prescription()
    {
        return $this->belongsTo(Prescription::class);
    }

    /**
     * Get the medicine for this item.
     */
    public function medicine()
    {
        return $this->belongsTo(Medicine::class);
    }
}
