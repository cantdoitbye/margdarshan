<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivitySkill extends Model
{
    protected $fillable = [
        'category',
        'skill_name',
        'description',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get active skills grouped by category
     */
    public static function getGroupedSkills()
    {
        return self::where('is_active', true)
            ->orderBy('category')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('category');
    }

    /**
     * Get all active skills as flat list
     */
    public static function getActiveSkills()
    {
        return self::where('is_active', true)
            ->orderBy('category')
            ->orderBy('sort_order')
            ->get();
    }
}
