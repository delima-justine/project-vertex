<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $table = 'tbl_notifications';

    protected $fillable = [
        'user_id',
        'office_id',
        'request_id',
        'action',
        'message',
        'read_at',
    ];

    // Cast read_at to a datetime
    protected $casts = [
        'read_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    public function office()
    {
        return $this->belongsTo(Office::class, 'office_id');
    }

    public function supplyRequest()
    {
        return $this->belongsTo(SupplyRequest::class, 'request_id');
    }
}
