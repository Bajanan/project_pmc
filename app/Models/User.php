<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    const PATIENT = "Patient";
    const SUPPLIER = "Supplier";
    const DOCTOR = "Doctor";
    const STAFF = "Staff";
    Const ADMIN  = "Admin";
    Const MANAGER  = "Manager";

    protected $fillable = [
        'user_title',
        'name',
        'reg_no',
        'email',
        'password',
        'contact_number',
        'DOB',
        'gender',
        'address',
        'user_role',
        'medical_history',
        'credit_limit',
        'credit_due',
        'active_status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
}
