<?php

namespace Craigzearfoss\UserRatings;

use App\User;
use Illuminate\Database\Eloquent\Model;

class UserRating extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'user_ratings';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'entity_id',
        'comment',
        'dislike',
        'favorite',
        'like',
        'namespace',
        'score',
        'user_id'
    ];

    /**
     * All nullable fields.
     *
     * @var array
     */
    protected static $nullable = [
        'comment',
        'rating'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * Get the user associated with the given rating.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function prepareData($data)
    {
        foreach (static::$nullable as $field) {
            if (isset($data[$field]) && strlen($data[$field]) == 0) {
                $data[$field] = null;
            }
        }

        return $data;
    }
}
