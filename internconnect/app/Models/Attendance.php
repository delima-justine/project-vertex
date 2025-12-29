<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $table = 'tbl_attendance';
    protected $primaryKey = 'attendance_id';

    protected $fillable = [
        'user_id',
        'time_in',
        'time_out',
        'total_hours',
        'is_late',
        'deduction_amount',
    ];

    protected $casts = [
        'time_in' => 'datetime',
        'time_out' => 'datetime',
        'is_late' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}