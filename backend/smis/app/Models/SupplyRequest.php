<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplyRequest extends Model
{
    protected $table = 'tbl_request';

    protected $fillable = [
        'user_id',
        'batch_id',
        'supply_id',
        'quantity_req',
        'purpose',
        'status',
        'approved_by'
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function supply()
    {
        return $this->belongsTo(Supply::class, 'supply_id', 'stock_num');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function archive()
    {
        return $this->hasOne(Archive::class, 'original_id')->where('table_name', 'tbl_request');
    }
}
