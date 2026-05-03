<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $table = 'tbl_permissions';

    protected $fillable = [
        'name',
        'description',
    ];

    public $timestamps = false;

    public function roles()
    {
        return $this->belongsToMany(Roles::class, 'tbl_role_permission', 'permission_id', 'role_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'tbl_user_permission', 'permission_id', 'user_id');
    }
}
