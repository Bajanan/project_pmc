<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;
    const PENDING = "Pending";
    const CANCELLED = "Cancelled";
    const COMPLETED = "Completed";

    const MORNING = "Morning";
    const LUNCH = "Lunch";
    const EVENING = "Evening";

    protected $with  = [
        "patient",
        'doctor'
    ];

    protected $fillable = [
        'doctor_id',
        'patient_id',
        'shift',
        'date',
        'time',
        'token_number',
        'status',
        'doctor_payment',
        'hospital_fee'

    ];

    public function patient(){
        return $this->belongsTo(User::class, 'patient_id');
    }

    public function doctor(){
        return $this->belongsTo(User::class, 'doctor_id');
    }
}
