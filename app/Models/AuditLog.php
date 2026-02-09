<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AuditLog extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'action',
        'description',
        'subject_type',
        'subject_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'url',
        'method',
        'status',
        'user_id',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the user who performed the action
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the subject of the activity
     */
    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Scope for successful logs
     */
    public function scopeSuccess($query)
    {
        return $query->where('status', 'success');
    }

    /**
     * Scope for failed logs
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope for warning logs
     */
    public function scopeWarning($query)
    {
        return $query->where('status', 'warning');
    }

    /**
     * Scope for specific action
     */
    public function scopeAction($query, $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope for specific user
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for specific model
     */
    public function scopeForModel($query, $model)
    {
        return $query->where('subject_type', $model);
    }

    /**
     * Get human readable time difference
     */
    public function getTimeAgoAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * Get status badge color
     */
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'success' => 'green',
            'failed' => 'red',
            'warning' => 'yellow',
            default => 'gray',
        };
    }

    /**
     * Get formatted old values
     */
    public function getFormattedOldValuesAttribute()
    {
        if (!$this->old_values) {
            return null;
        }

        $values = [];
        foreach ($this->old_values as $key => $value) {
            $values[] = "<strong>" . str_replace('_', ' ', ucfirst($key)) . ":</strong> " 
                . (is_array($value) ? json_encode($value) : $value);
        }

        return implode('<br>', $values);
    }

    /**
     * Get formatted new values
     */
    public function getFormattedNewValuesAttribute()
    {
        if (!$this->new_values) {
            return null;
        }

        $values = [];
        foreach ($this->new_values as $key => $value) {
            $values[] = "<strong>" . str_replace('_', ' ', ucfirst($key)) . ":</strong> " 
                . (is_array($value) ? json_encode($value) : $value);
        }

        return implode('<br>', $values);
    }

    /**
     * Get model name
     */
    public function getModelNameAttribute()
    {
        if (!$this->subject_type) {
            return 'System';
        }

        return class_basename($this->subject_type);
    }
}