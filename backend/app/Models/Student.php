<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'customer_id',
        'name',
        'class',
        'subjects',
        'board',
        'tuition_type',
        'learning_goal',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'subjects' => 'array',
    ];

    /**
     * Get the customer that owns the student.
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
