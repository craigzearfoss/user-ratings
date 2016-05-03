<?php

/**
 * Part of the UserRatings package.
 *
 * @package    UserRatings
 * @version    0.0.0
 * @author     Craig Zearfoss
 * @license    MIT License
 * @copyright  (c) 2011-2016, Craig Zearfoss
 * @link       http://craigzearfoss.com
 */

namespace Craigzearfoss\UserRatings;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class IlluminateUserRating extends Model
{
    /**
     * {@inheritdoc}
     */
    public $timestamps = true;

    /**
     * {@inheritdoc}
     */
    public $table = 'user_ratings';

    /**
     * {@inheritdoc}
     */
    protected $fillable = [
        'entity_id',
        'comment',
        'dislike',
        'favorite',
        'like',
        'namespace',
        'rating',
        'user_id'
    ];

    /**
     * Returns the polymorphic relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function userRatable()
    {
        return $this->morphTo();
    }

    /**
     * Finds a user rating by it's user id.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  integer  $userId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUserId(Builder $query, $userId)
    {
        return $query->whereUserId($userId);
    }
}
