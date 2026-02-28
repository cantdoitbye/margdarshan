<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DemoBooking extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'tutor_id',
        'student_id',
        'demo_date',
        'demo_time',
        'status', // pending, confirmed, completed, cancelled
        'notes',
        'tutor_response',
        'responded_at'
    ];

    protected $casts = [
        'demo_date' => 'date',
        'demo_time' => 'datetime',
        'responded_at' => 'datetime',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function tutor()
    {
        return $this->belongsTo(TutorProfile::class, 'tutor_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
