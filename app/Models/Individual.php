<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Individual extends Model
{
    // Traits used by the model
    use HasFactory, Notifiable, HasApiTokens, SoftDeletes;
    
    // The table associated with the model
    protected $table = 'individuals';

    // The primary key associated with the table
    protected $primaryKey = 'id'; // Note: Corrected property name from `primarykey` to `primaryKey`

    // Attributes that are mass assignable
    protected $fillable = ['user_name', 'email', 'contact_number', 'address', 'password', 'profile_photo'];

    // Indicates if the model should be timestamped
    public $timestamps = false;

}
