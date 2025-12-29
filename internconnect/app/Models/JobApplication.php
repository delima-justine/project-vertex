<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobApplication extends Model
{
    protected $table = 'tbl_job_application';
    protected $primaryKey = 'application_id';

    protected $fillable = [
        'job_id',
        'user_id',
        'application_date',
        'resume_file_url',
        'cover_letter_file_url',
        'hr_status',
    ];

    protected $casts = [
        'application_date' => 'datetime',
    ];

    public function job()
    {
        return $this->belongsTo(JobPosting::class, 'job_id', 'job_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}