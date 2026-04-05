<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    protected $table = 'tbl_unit';

    // Define the fillable fields in the table
    protected $fillable = [
        'unit_name',
    ];

    // Disable timestamps if not using created_at and updated_at columns
    public $timestamps = false;
}
