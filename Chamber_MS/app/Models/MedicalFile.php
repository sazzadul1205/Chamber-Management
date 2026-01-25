<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicalFile extends Model
{
    use HasFactory;

    /**
     * Mass assignable attributes
     */
    protected $fillable = [
        'file_code',
        'treatment_id',
        'patient_id',
        'file_type',
        'file_name',
        'file_path',
        'file_size',
        'description',
        'uploaded_at',
        'uploaded_by',
        'is_confidential',
    ];

    /**
     * Attribute casting
     */
    protected $casts = [
        'uploaded_at'     => 'datetime',
        'is_confidential' => 'boolean',
        'file_size'       => 'integer',
    ];

    // ==================================================
    // RELATIONSHIPS
    // ==================================================

    public function treatment()
    {
        return $this->belongsTo(Treatment::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    // ==================================================
    // FILE CODE GENERATION
    // ==================================================

    /**
     * Generate sequential medical file code (MF0001)
     */
    public static function generateFileCode(): string
    {
        $lastNumber = self::query()
            ->selectRaw("MAX(CAST(SUBSTRING(file_code, 3) AS UNSIGNED)) as max_code")
            ->value('max_code');

        $next = ($lastNumber ?? 0) + 1;

        return 'MF' . str_pad($next, 4, '0', STR_PAD_LEFT);
    }

    // ==================================================
    // FILE TYPE DEFINITIONS
    // ==================================================

    /**
     * Central file type config
     */
    public static function fileTypes(): array
    {
        return [
            'xray'         => ['label' => 'X-Ray',        'color' => 'sky'],
            'photo'        => ['label' => 'Photo',        'color' => 'blue'],
            'document'     => ['label' => 'Document',     'color' => 'gray'],
            'prescription' => ['label' => 'Prescription', 'color' => 'green'],
            'report'       => ['label' => 'Report',       'color' => 'amber'],
            'other'        => ['label' => 'Other',        'color' => 'zinc'],
        ];
    }

    // ==================================================
    // ACCESSORS
    // ==================================================

    /**
     * File type label (UI-safe)
     */
    public function getFileTypeLabelAttribute(): string
    {
        return self::fileTypes()[$this->file_type]['label']
            ?? ucfirst($this->file_type);
    }

    /**
     * Tailwind color keyword (no HTML here)
     */
    public function getFileTypeColorAttribute(): string
    {
        return self::fileTypes()[$this->file_type]['color']
            ?? 'gray';
    }

    /**
     * Human-readable file size
     */
    public function getFileSizeFormattedAttribute(): string
    {
        $bytes = $this->file_size;

        return match (true) {
            $bytes >= 1073741824 => number_format($bytes / 1073741824, 2) . ' GB',
            $bytes >= 1048576    => number_format($bytes / 1048576, 2) . ' MB',
            $bytes >= 1024       => number_format($bytes / 1024, 2) . ' KB',
            default              => $bytes . ' bytes',
        };
    }
}
