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

use Illuminate\Database\Eloquent\Builder;

interface UserRatableInterface
{
    /**
     * Sets the Eloquent user ratings model comment.
     *
     * @param  string  $model
     * @return void
     */
    public static function setUserRatingsModel($model);

    /**
     * Returns the entity Eloquent user rating model object.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function userRatings();

    /**
     * Returns all the user ratings under the entity namespace.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function allUserRatings();

    /**
     * Attaches multiple user ratings to the entity.
     *
     * @param  string|array  $userRatings
     * @return bool
     */
    public function userRating($userRatings);

    /**
     * Attaches or detaches the given user ratings.
     *
     * @param  string|array  $userRatings
     * @param  string  $type
     * @return bool
     */
    public function setUserRatings($userRatings, $type = 'comment');

    /**
     * Attaches the given user rating to the entity.
     *
     * @param  string  $comment
     * @return void
     */
    public function addUserRating($comment);

    /**
     * Detaches the given user rating from the entity.
     *
     * @param  string  $comment
     * @return void
     */
    public function removeUserRating($comment);

    /**
     * Creates a new model instance.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public static function createUserRatingsModel();

    /**
     * @param array $newUserRatings
     * @return bool
     */
    public function syncUserRatings($newUserRatings = []);
}
