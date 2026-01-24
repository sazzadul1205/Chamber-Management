<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLogMetadata extends Model
{
    use HasFactory;

    protected $fillable = [
        'audit_log_id',
        'key',
        'value'
    ];

    /**
     * Get the audit log that owns this metadata
     */
    public function auditLog(): BelongsTo
    {
        return $this->belongsTo(AuditLog::class);
    }
}
