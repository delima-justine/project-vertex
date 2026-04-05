<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Office extends Model
{
    protected $table = 'tbl_office';

    protected $fillable = [
        'office_name',
    ];

    public $timestamps = false;
}
