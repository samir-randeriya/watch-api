<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User_details extends Model
{
    use HasFactory, Notifiable, HasApiTokens, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "users_details";

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'company_name',     // Company name if applicable
        'type',             // Type of user (e.g., individual, company)
        'user_name',        // Name of the user
        'email',            // Email address of the user
        'contact_number',   // Contact number of the user
        'company_number',   // Company number if applicable
        'address',          // Address of the user
        'password',         // Password of the user
        'profile_photo',    // Profile photo of the user
        'created_at',       // Timestamp of creation
        'device_token',     // Token for device identification
        'user_id',          // User ID for relationships
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Get the product associated with the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function watchDetail()
    {
        return $this->hasOne(Product::class, 'user_id', 'id');
    }

    /**
     * Get the enquiries associated with the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function enquiries()
    {
        return $this->hasMany(Enquiry::class, 'user_id', 'id');
    }
}
