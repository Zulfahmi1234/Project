<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FavoriteLocation extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'favorite_locations';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'city_name',
        'latitude',
        'longitude',
        'country',
        'country_code',
        'timezone',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'latitude'  => 'decimal:7',
            'longitude' => 'decimal:7',
        ];
    }

    /**
     * Get the user that owns the favorite location.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
