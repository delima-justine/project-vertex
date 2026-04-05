<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Archive extends Model
{
    protected $table = 'tbl_archive';

    protected $fillable = [
        'table_name',
        'original_id',
        'data',
        'archived_by',
    ];

    // Disable timestamps for this model
    public $timestamps = false;

    // Automatically cast JSON data to a PHP array
    protected $casts = [
        'data' => 'array',
        'archived_at' => 'datetime',
    ];

    // Relationship to the User who archived the record
    public function archiver()
    {
        return $this->belongsTo(User::class, 'archived_by');
    }
}
