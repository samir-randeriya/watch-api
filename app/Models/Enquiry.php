<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Enquiry extends Model
{
    // Traits used by the model
    use HasFactory, Notifiable, HasApiTokens, SoftDeletes;
    
    // The table associated with the model
    protected $table = 'enquiry';

    // The primary key associated with the table
    protected $primaryKey = 'id'; // Note: Corrected property name from `primarykey` to `primaryKey`

    // Attributes that are mass assignable
    protected $fillable = ['watch_id', 'user_id', 'price', 'status'];

    // Indicates if the model should be timestamped
    public $timestamps = false;

    // Define the relationship between Enquiry and User_details
    public function user()
    {
        return $this->belongsTo(User_details::class, 'user_id');
    }
}
