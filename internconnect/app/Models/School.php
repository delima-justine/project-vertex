<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class School extends Model
{
    protected $table = 'tbl_school';
    protected $primaryKey = 'school_id';

    protected $fillable = [
        'school_name',
        'branch_campus',
        'address',
    ];

    public function coordinators()
    {
        return $this->hasMany(Coordinator::class, 'school_id', 'school_id');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'school_id', 'school_id');
    }
}