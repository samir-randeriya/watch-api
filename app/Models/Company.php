<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Company extends Model
{
    // Traits used by the model
    use HasFactory, Notifiable, HasApiTokens, SoftDeletes;

    // Table associated with the model
    protected $table = "companies";

    // Primary key for the model
    protected $primaryKey = 'id';

    // Attributes that are mass assignable
    protected $fillable = [
        'company_name',
        'email',
        'contact_number',
        'company_number',
        'company_address',
        'password',
        'profile_photo',
        'condition'
    ];

    // Indicates if the model should be timestamped
    public $timestamps = false;
}
