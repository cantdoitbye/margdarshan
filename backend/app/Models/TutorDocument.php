<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TutorDocument extends Model
{
    protected $fillable = [
        'user_id',
        'document_type',
        'document_name',
        'document_path',
        'file_size',
        'verification_status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
