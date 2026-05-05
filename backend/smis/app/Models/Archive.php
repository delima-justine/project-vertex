<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $table_name
 * @property int $original_id
 * @property array $data
 * @property int $archived_by
 * @property \Illuminate\Support\Carbon $archived_at
 */
class Archive extends Model
{
    protected $table = 'tbl_archive';

    protected $fillable = [
        'table_name',
        'original_id',
        'data',
        'archived_by',
        'archived_at',
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
