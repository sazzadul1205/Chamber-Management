<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    // Add fillable fields
    protected $fillable = ['name'];

    // Add relationship to User model
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
