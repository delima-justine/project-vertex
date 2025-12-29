<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $table = 'tbl_document';
    protected $primaryKey = 'doc_id';

    protected $fillable = [
        'user_id',
        'doc_type',
        'submission_date',
        'due_date',
        'verification_status',
        'file_url',
    ];

    protected $casts = [
        'submission_date' => 'date',
        'due_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}