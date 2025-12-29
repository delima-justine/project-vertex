<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobPosting extends Model
{
    protected $table = 'tbl_job_posting';
    protected $primaryKey = 'job_id';

    protected $fillable = [
        'title',
        'description',
        'requirements',
        'department',
        'salary_range',
        'posted_by_user_id',
        'post_date',
    ];

    protected $casts = [
        'post_date' => 'date',
    ];

    public function postedBy()
    {
        return $this->belongsTo(User::class, 'posted_by_user_id', 'user_id');
    }

    public function applications()
    {
        return $this->hasMany(JobApplication::class, 'job_id', 'job_id');
    }
}