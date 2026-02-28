<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TutorProfile extends Model
{
    protected $fillable = [
        'user_id',
        'tutor_type',
        'bio',
        'subjects',
        'classes',
        'activity_skills',
        'demo_video_path',
        'education',
        'experience_years',
        'hourly_rate',
        'language',
        'timezone',
        'availability',
        'profile_image',
        'is_profile_complete',
        'service_location',
        'service_latitude',
        'service_longitude',
        'service_radius_km',
    ];

    protected $casts = [
        'subjects' => 'array',
        'classes' => 'array',
        'activity_skills' => 'array',
        'availability' => 'array',
        'is_profile_complete' => 'boolean',
        'hourly_rate' => 'decimal:2',
    ];

    /**
     * Check if tutor is academic type
     */
    public function isAcademic()
    {
        return $this->tutor_type === 'academic';
    }

    /**
     * Check if tutor is activity type
     */
    public function isActivity()
    {
        return $this->tutor_type === 'activity';
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the qualifications for the tutor.
     */
    public function qualifications()
    {
        return $this->hasMany(TutorQualification::class);
    }

    /**
     * Get unique boards the tutor teaches.
     */
    public function getBoards()
    {
        return $this->qualifications()
                    ->with('board')
                    ->get()
                    ->pluck('board')
                    ->unique('id')
                    ->values();
    }

    /**
     * Get classes for a specific board.
     */
    public function getClassesForBoard($boardId)
    {
        return $this->qualifications()
                    ->where('board_id', $boardId)
                    ->with('classLevel')
                    ->get()
                    ->pluck('classLevel')
                    ->unique('id')
                    ->values();
    }

    /**
     * Get subjects for a specific board and class.
     */
    public function getSubjectsForBoardClass($boardId, $classId)
    {
        return $this->qualifications()
                    ->where('board_id', $boardId)
                    ->where('class_id', $classId)
                    ->with('subject')
                    ->get()
                    ->pluck('subject')
                    ->unique('id')
                    ->values();
    }
}
