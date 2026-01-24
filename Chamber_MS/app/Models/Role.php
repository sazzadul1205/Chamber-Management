<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    use HasFactory, SoftDeletes;

    // =======================
    // Mass Assignable Fields
    // =======================
    protected $fillable = [
        'name',
    ];

    // =======================
    // Hidden Fields
    // =======================
    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    // =======================
    // Relationships
    // =======================

    /**
     * Users that belong to this role
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    // =======================
    // Helper Methods
    // =======================

    /**
     * Get Tailwind badge for role
     */
    public function getBadge()
    {
        $colors = [
            'Super Admin' => 'red',
            'Admin' => 'yellow',
            'Doctor' => 'green',
            'Staff' => 'blue',
        ];

        $color = $colors[$this->name] ?? 'gray';

        return '<span class="bg-' . $color . '-100 text-' . $color . '-800 text-xs font-semibold mr-2 px-2.5 py-0.5 rounded">'
            . $this->name .
            '</span>';
    }
}
