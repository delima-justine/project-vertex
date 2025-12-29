<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $table = 'tbl_notification';
    protected $primaryKey = 'notification_id';

    protected $fillable = [
        'user_id',
        'type',
        'message',
        'send_date',
        'is_read',
    ];

    protected $casts = [
        'send_date' => 'datetime',
        'is_read' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}