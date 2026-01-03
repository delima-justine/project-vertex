<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coordinator extends Model
{
    protected $table = 'tbl_coordinator';
    protected $primaryKey = 'coordinator_id';

    protected $fillable = [
        'coordinator_id',
        'first_name',
        'last_name',
        'email',
        'school_id',
        'unique_key',
    ];

    public function school()
    {
        return $this->belongsTo(School::class, 'school_id', 'school_id');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'coordinator_id', 'coordinator_id');
    }
}