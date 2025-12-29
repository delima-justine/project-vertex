<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coordinator extends Model
{
    protected $table = 'tbl_coordinator';
    protected $primaryKey = 'coordinator_id';

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'school_id',
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