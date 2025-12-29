<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Progress extends Model
{
    protected $table = 'tbl_progress';
    protected $primaryKey = 'progress_id';

    protected $fillable = [
        'user_id',
        'required_hours',
        'logged_hours',
        'milestone',
        'milestone_achieved_date',
        'evaluation_score',
    ];

    protected $casts = [
        'milestone_achieved_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}