<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Roles extends Model
{
    protected $table = 'tbl_roles';

    // Define the fillable fields
    protected $fillable = [
        'role_name',
    ];

    // Disable timestamps if not using created_at and updated_at columns
    public $timestamps = false;

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'tbl_role_permission', 'role_id', 'permission_id');
    }
}
