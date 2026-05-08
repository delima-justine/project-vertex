<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminAudit extends Model
{
    protected $table = 'tbl_admin_audit';

    protected $fillable = [
        'admin_id',
        'admin_name',
        'admin_role',
        'action_type',
        'target_id',
        'target_type',
        'target_name',
        'old_values',
        'new_values',
        'description',
        'ip_address',
        'user_agent',
        'performed_at',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'performed_at' => 'datetime',
    ];

    /**
     * Get the admin user who performed the action.
     */
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
