<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TutorQualification extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'tutor_profile_id',
        'board_id',
        'class_id',
        'subject_id',
    ];

    /**
     * Get the tutor profile that owns the qualification.
     */
    public function tutorProfile()
    {
        return $this->belongsTo(TutorProfile::class);
    }

    /**
     * Get the board for this qualification.
     */
    public function board()
    {
        return $this->belongsTo(Board::class);
    }

    /**
     * Get the class level for this qualification.
     */
    public function classLevel()
    {
        return $this->belongsTo(ClassLevel::class, 'class_id');
    }

    /**
     * Get the subject for this qualification.
     */
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
}
