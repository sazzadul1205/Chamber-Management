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
        'status',
        'requested_date',
        'requested_by',
        'requested_notes',
        'expected_delivery_date',
        'uploaded_at',
        'uploaded_by',
        'is_confidential',
    ];

    protected $casts = [
        'uploaded_at' => 'datetime',
        'requested_date' => 'datetime',
        'expected_delivery_date' => 'datetime',
        'is_confidential' => 'boolean',
        'file_size' => 'integer',
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

    public function requestedBy()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    // Status Constants
    const STATUS_REQUESTED = 'requested';
    const STATUS_PENDING = 'pending';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    // File Type Constants
    const TYPE_XRAY = 'xray';
    const TYPE_LAB_REPORT = 'lab_report';
    const TYPE_CT_SCAN = 'ct_scan';
    const TYPE_PHOTO = 'photo';
    const TYPE_PRESCRIPTION = 'prescription';
    const TYPE_REPORT = 'report';
    const TYPE_OTHER = 'other';

    // Scopes
    public function scopeRequested($query)
    {
        return $query->where('status', self::STATUS_REQUESTED);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    public function scopeForTreatment($query, $treatmentId)
    {
        return $query->where('treatment_id', $treatmentId);
    }

    public function scopeForPatient($query, $patientId)
    {
        return $query->where('patient_id', $patientId);
    }

    // Static Methods
    public static function statuses(): array
    {
        return [
            self::STATUS_REQUESTED => 'Requested',
            self::STATUS_PENDING => 'Pending',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_CANCELLED => 'Cancelled',
        ];
    }

    public static function fileTypes(): array
    {
        return [
            self::TYPE_XRAY => 'X-Ray',
            self::TYPE_LAB_REPORT => 'Lab Report',
            self::TYPE_CT_SCAN => 'CT Scan',
            self::TYPE_PHOTO => 'Photo',
            self::TYPE_PRESCRIPTION => 'Prescription',
            self::TYPE_REPORT => 'Report',
            self::TYPE_OTHER => 'Other',
        ];
    }

    public static function generateFileCode(): string
    {
        $last = self::orderByDesc('id')->first();
        $next = ($last ? $last->id : 0) + 1;
        return 'MF' . str_pad($next, 4, '0', STR_PAD_LEFT);
    }

    // Accessors
    public function getStatusTextAttribute(): string
    {
        return self::statuses()[$this->status] ?? ucfirst($this->status);
    }

    public function getStatusColorAttribute(): string
    {
        $colors = [
            self::STATUS_REQUESTED => 'blue',
            self::STATUS_PENDING => 'yellow',
            self::STATUS_COMPLETED => 'green',
            self::STATUS_CANCELLED => 'red',
        ];

        return $colors[$this->status] ?? 'gray';
    }

    public function getFileTypeTextAttribute(): string
    {
        return self::fileTypes()[$this->file_type] ?? ucfirst($this->file_type);
    }

    public function getFileTypeColorAttribute(): string
    {
        $colors = [
            self::TYPE_XRAY => 'blue',
            self::TYPE_LAB_REPORT => 'green',
            self::TYPE_CT_SCAN => 'purple',
            self::TYPE_PHOTO => 'pink',
            self::TYPE_PRESCRIPTION => 'indigo',
            self::TYPE_REPORT => 'orange',
            self::TYPE_OTHER => 'gray',
        ];

        return $colors[$this->file_type] ?? 'gray';
    }

    public function getFileSizeFormattedAttribute(): string
    {
        if (!$this->file_size) return '0 KB';

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

    public function getIsUploadedAttribute(): bool
    {
        return !empty($this->file_path) && !empty($this->uploaded_at);
    }

    // Methods
    public function requestTest(array $data)
    {
        $this->update([
            'status' => self::STATUS_REQUESTED,
            'requested_date' => now(),
            'requested_by' => auth()->id(),
            'requested_notes' => $data['notes'] ?? null,
            'expected_delivery_date' => $data['expected_date'] ?? null,
        ]);
    }

    public function uploadFile($file, $description = null)
    {
        $path = $file->store(
            'medical_files/' . now()->format('Y/m'),
            'public'
        );

        $this->update([
            'status' => self::STATUS_COMPLETED,
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'file_size' => $file->getSize(),
            'description' => $description,
            'uploaded_at' => now(),
            'uploaded_by' => auth()->id(),
        ]);
    }

    public function markAsPending()
    {
        $this->update(['status' => self::STATUS_PENDING]);
    }

    public function cancelRequest()
    {
        $this->update(['status' => self::STATUS_CANCELLED]);
    }
}
