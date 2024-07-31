<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Product extends Model
{
    use HasFactory, Notifiable, HasApiTokens, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "product";

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
        'brand_name',       // Brand name of the product
        'type',             // Type of the product
        'year',             // Year of manufacture
        'item_name',        // Name of the product item
        'description',      // Description of the product
        'watch_pic1',       // URL or path to the first product picture
        'watch_pic2',       // URL or path to the second product picture
        'watch_pic3',       // URL or path to the third product picture
        'watch_pic4',       // URL or path to the fourth product picture
        'watch_pic5',       // URL or path to the fifth product picture
        'watch_pic6',       // URL or path to the sixth product picture
        'user_id',          // Foreign key referencing the user
        'reference_no',     // Reference number for the product
        'negotiation',      // Negotiation details or status
        'accessories',      // Accessories included with the product
        'country',          // Country of origin
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Get the user associated with the product.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function userDetail()
    {
        return $this->belongsTo(User_details::class, 'user_id');
    }

    /**
     * Get the enquiries associated with the product.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function enquiries()
    {
        return $this->hasMany(Enquiry::class, 'watch_id');
    }
}