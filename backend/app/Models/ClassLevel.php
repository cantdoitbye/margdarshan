<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassLevel extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'classes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
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
     * Get the subjects available for this class.
     */
    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'class_subject', 'class_id', 'subject_id')
                    ->withTimestamps()
                    ->withPivot('is_active')
                    ->wherePivot('is_active', true)
                    ->orderBy('sort_order')
                    ->orderBy('name');
    }

    /**
     * Get all subjects (including inactive) for this class.
     */
    public function allSubjects()
    {
        return $this->belongsToMany(Subject::class, 'class_subject', 'class_id', 'subject_id')
                    ->withTimestamps()
                    ->withPivot('is_active');
    }

    /**
     * Scope a query to only include active classes.
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
     * Get the chapters for this class.
     */
    public function chapters()
    {
        return $this->hasMany(Chapter::class, 'class_id');
    }

    /**
     * Get the quizzes for this class.
     */
    public function quizzes()
    {
        return $this->hasMany(Quiz::class, 'class_id');
    }
}
