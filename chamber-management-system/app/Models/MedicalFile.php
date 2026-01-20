<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicalFile extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'treatment_id',
        'file_type',
        'file_path',
        'uploaded_at',
        'uploaded_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'uploaded_at' => 'datetime',
    ];

    /**
     * Automatically set uploaded_at and uploaded_by.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($file) {
            if (empty($file->uploaded_at)) {
                $file->uploaded_at = now();
            }

            if (auth()->check() && empty($file->uploaded_by)) {
                $file->uploaded_by = auth()->id();
            }
        });
    }

    /**
     * Get the treatment that owns the medical file.
     */
    public function treatment()
    {
        return $this->belongsTo(Treatment::class);
    }

    /**
     * Get the user who uploaded the file.
     */
    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Get file type icon.
     */
    public function getFileTypeIconAttribute()
    {
        $icons = [
            'xray' => 'bi-file-earmark-medical',
            'report' => 'bi-file-text',
            'image' => 'bi-image',
        ];

        return $icons[$this->file_type] ?? 'bi-file-earmark';
    }

    /**
     * Get file type color.
     */
    public function getFileTypeColorAttribute()
    {
        $colors = [
            'xray' => 'primary',
            'report' => 'info',
            'image' => 'success',
        ];

        return $colors[$this->file_type] ?? 'secondary';
    }
}
