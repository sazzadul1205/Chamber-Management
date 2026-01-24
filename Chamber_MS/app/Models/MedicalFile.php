<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicalFile extends Model
{
    use HasFactory;

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
        'is_confidential'
    ];

    protected $casts = [
        'uploaded_at' => 'datetime',
        'is_confidential' => 'boolean',
        'file_size' => 'integer'
    ];

    // Relationships
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

    // Generate file code
    public static function generateFileCode()
    {
        $latest = self::latest()->first();
        $number = $latest ? intval(substr($latest->file_code, 2)) + 1 : 1;
        return 'MF' . str_pad($number, 4, '0', STR_PAD_LEFT);
    }

    // Get file type badge
    public function getFileTypeBadgeAttribute()
    {
        $badges = [
            'xray' => 'badge bg-info',
            'photo' => 'badge bg-primary',
            'document' => 'badge bg-secondary',
            'prescription' => 'badge bg-success',
            'report' => 'badge bg-warning',
            'other' => 'badge bg-dark'
        ];
        return '<span class="' . ($badges[$this->file_type] ?? 'badge bg-secondary') . '">' . ucfirst($this->file_type) . '</span>';
    }

    // Get file size in human readable format
    public function getFileSizeFormattedAttribute()
    {
        $bytes = $this->file_size;
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }
}
