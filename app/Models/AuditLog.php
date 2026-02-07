<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'user_role',
        'action',
        'table_name',
        'record_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'action_time',
        'url',
        'method',
        'description'
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'action_time' => 'datetime',
    ];

    protected $appends = [
        'action_icon',
        'action_color',
        'changes_summary',
        'record_link',
    ];

    /**
     * Get the user who performed the action
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get metadata for this audit log
     */
    public function metadata(): HasMany
    {
        return $this->hasMany(AuditLogMetadata::class);
    }

    /**
     * Scope: Filter by user
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope: Filter by table
     */
    public function scopeForTable($query, $tableName)
    {
        return $query->where('table_name', $tableName);
    }

    /**
     * Scope: Filter by action
     */
    public function scopeForAction($query, $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope: Filter by date range
     */
    public function scopeDateRange($query, $startDate, $endDate = null)
    {
        $query->whereDate('action_time', '>=', $startDate);

        if ($endDate) {
            $query->whereDate('action_time', '<=', $endDate);
        }

        return $query;
    }

    /**
     * Scope: Search in audit logs
     */
    public function scopeSearch($query, $searchTerm)
    {
        return $query->where(function ($q) use ($searchTerm) {
            $q->where('action', 'like', "%{$searchTerm}%")
                ->orWhere('table_name', 'like', "%{$searchTerm}%")
                ->orWhere('description', 'like', "%{$searchTerm}%")
                ->orWhereHas('user', function ($q) use ($searchTerm) {
                    $q->where('full_name', 'like', "%{$searchTerm}%");
                });
        });
    }

    /**
     * Get icon for action type
     */
    public function getActionIconAttribute(): string
    {
        $icons = [
            'created' => 'fas fa-plus-circle',
            'updated' => 'fas fa-edit',
            'deleted' => 'fas fa-trash',
            'restored' => 'fas fa-trash-restore',
            'viewed' => 'fas fa-eye',
            'downloaded' => 'fas fa-download',
            'printed' => 'fas fa-print',
            'logged_in' => 'fas fa-sign-in-alt',
            'logged_out' => 'fas fa-sign-out-alt',
            'exported' => 'fas fa-file-export',
            'imported' => 'fas fa-file-import',
        ];

        return $icons[$this->action] ?? 'fas fa-info-circle';
    }

    /**
     * Get color for action type
     */
    public function getActionColorAttribute(): string
    {
        $colors = [
            'created' => 'success',
            'updated' => 'warning',
            'deleted' => 'danger',
            'restored' => 'info',
            'viewed' => 'primary',
            'downloaded' => 'secondary',
            'printed' => 'dark',
            'logged_in' => 'success',
            'logged_out' => 'secondary',
            'exported' => 'info',
            'imported' => 'primary',
        ];

        return $colors[$this->action] ?? 'secondary';
    }

    /**
     * Get summary of changes
     */
    public function getChangesSummaryAttribute(): string
    {
        if (empty($this->old_values) && empty($this->new_values)) {
            return 'No field changes';
        }

        $changes = [];

        if ($this->old_values && $this->new_values) {
            foreach ($this->new_values as $key => $value) {
                $oldValue = $this->old_values[$key] ?? null;
                if ($oldValue != $value) {
                    $changes[] = $key;
                }
            }
        }

        if (empty($changes)) {
            return 'Updated record';
        }

        return 'Changed: ' . implode(', ', array_slice($changes, 0, 3)) .
            (count($changes) > 3 ? '...' : '');
    }

    /**
     * Get link to the affected record
     */
    public function getRecordLinkAttribute(): ?string
    {
        $routes = [
            'users' => 'users.show',
            'patients' => 'patients.show',
            'doctors' => 'doctors.show',
            'appointments' => 'appointments.show',
            'treatments' => 'treatments.show',
            'invoices' => 'invoices.show',
            'payments' => 'payments.show',
            'receipts' => 'receipts.show',
            'prescriptions' => 'prescriptions.show',
            'inventory_items' => 'inventory-items.show',
        ];

        if (isset($routes[$this->table_name])) {
            return route($routes[$this->table_name], $this->record_id);
        }

        return null;
    }

    /**
     * Get human-readable description of the action
     */
    public function getHumanDescriptionAttribute(): string
    {
        $userName = $this->user ? $this->user->full_name : 'System';
        $tableName = str_replace('_', ' ', $this->table_name);
        $tableName = ucwords($tableName);

        $descriptions = [
            'created' => "{$userName} created a new {$tableName}",
            'updated' => "{$userName} updated a {$tableName}",
            'deleted' => "{$userName} deleted a {$tableName}",
            'restored' => "{$userName} restored a {$tableName}",
            'viewed' => "{$userName} viewed a {$tableName}",
            'logged_in' => "{$userName} logged in",
            'logged_out' => "{$userName} logged out",
        ];

        return $descriptions[$this->action] ?? "{$userName} performed {$this->action} on {$tableName}";
    }

    /**
     * Get detailed changes as HTML
     */
    public function getChangesHtmlAttribute(): string
    {
        if (empty($this->old_values) && empty($this->new_values)) {
            return '<span class="text-muted">No field changes</span>';
        }

        $html = '<div class="audit-changes">';

        if ($this->old_values && $this->new_values) {
            foreach ($this->new_values as $key => $newValue) {
                $oldValue = $this->old_values[$key] ?? null;

                if ($oldValue != $newValue) {
                    $formattedKey = str_replace('_', ' ', $key);
                    $formattedKey = ucwords($formattedKey);

                    $html .= '<div class="change-item mb-2">';
                    $html .= '<strong>' . e($formattedKey) . ':</strong><br>';
                    $html .= '<span class="text-danger"><del>' . e($this->formatValue($oldValue)) . '</del></span> ';
                    $html .= '<span class="text-success">' . e($this->formatValue($newValue)) . '</span>';
                    $html .= '</div>';
                }
            }
        } elseif ($this->new_values && $this->action === 'created') {
            $html .= '<div class="change-item">';
            $html .= '<strong>Created with:</strong><br>';
            foreach ($this->new_values as $key => $value) {
                $formattedKey = str_replace('_', ' ', $key);
                $formattedKey = ucwords($formattedKey);
                $html .= '<div>' . e($formattedKey) . ': ' . e($this->formatValue($value)) . '</div>';
            }
            $html .= '</div>';
        } elseif ($this->old_values && $this->action === 'deleted') {
            $html .= '<div class="change-item">';
            $html .= '<strong>Deleted record contained:</strong><br>';
            foreach ($this->old_values as $key => $value) {
                $formattedKey = str_replace('_', ' ', $key);
                $formattedKey = ucwords($formattedKey);
                $html .= '<div>' . e($formattedKey) . ': ' . e($this->formatValue($value)) . '</div>';
            }
            $html .= '</div>';
        }

        $html .= '</div>';
        return $html;
    }

    /**
     * Format value for display
     */
    private function formatValue($value): string
    {
        if (is_array($value)) {
            return json_encode($value, JSON_PRETTY_PRINT);
        }

        if (is_bool($value)) {
            return $value ? 'Yes' : 'No';
        }

        if (is_null($value)) {
            return '(empty)';
        }

        return (string) $value;
    }

    /**
     * Add metadata to audit log
     */
    public function addMetadata(string $key, $value): self
    {
        $this->metadata()->create([
            'key' => $key,
            'value' => is_array($value) ? json_encode($value) : $value,
        ]);

        return $this;
    }

    /**
     * Get metadata value
     */
    public function getMetadata(string $key, $default = null)
    {
        $metadata = $this->metadata()->where('key', $key)->first();

        if (!$metadata) {
            return $default;
        }

        $value = $metadata->value;

        // Try to decode JSON
        $decoded = json_decode($value, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            return $decoded;
        }

        return $value;
    }
}
