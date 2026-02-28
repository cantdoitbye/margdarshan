<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'category',
        'type',
        'sort_order',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Scope a query to only include active subjects.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to order by sort order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Scope a query to only include academic subjects.
     */
    public function scopeAcademic($query)
    {
        return $query->where('type', 'academic');
    }

    /**
     * Scope a query to only include activity subjects.
     */
    public function scopeActivity($query)
    {
        return $query->where('type', 'activity');
    }

    /**
     * Get the classes that have this subject.
     */
    public function classes()
    {
        return $this->belongsToMany(ClassLevel::class, 'class_subject', 'subject_id', 'class_id')
                    ->withTimestamps()
                    ->withPivot('is_active')
                    ->wherePivot('is_active', true);
    }

    /**
     * Get all classes (including inactive) that have this subject.
     */
    public function allClasses()
    {
        return $this->belongsToMany(ClassLevel::class, 'class_subject', 'subject_id', 'class_id')
                    ->withTimestamps()
                    ->withPivot('is_active');
    }

    /**
     * Get the chapters for this subject.
     */
    public function chapters()
    {
        return $this->hasMany(Chapter::class);
    }

    /**
     * Get the quizzes for this subject.
     */
    public function quizzes()
    {
        return $this->hasMany(Quiz::class);
    }
}
