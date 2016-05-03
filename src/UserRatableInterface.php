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
     * Attaches the given user rating to the entity.
     *
     * @param  integer $userId
     * @param  array   $params
     * @return void
     */
    public function addUserRating($userId, $params);

    /**
     * Creates a new model instance.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public static function createUserRatingsModel();

    /**
     * @param integer $userId
     * @return bool
     */
    public function deleteUserRating($userId);
}
