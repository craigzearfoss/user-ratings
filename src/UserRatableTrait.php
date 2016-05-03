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

trait UserRatableTrait
{
    /**
     * The Eloquent user ratings model name.
     *
     * @var string
     */
    protected static $userRatingsModel = 'Craigzearfoss\UserRatings\IlluminateUserRating';

    /**
     * {@inheritdoc}
     */
    public static function getUserRatingsModel()
    {
        return static::$userRatingsModel;
    }

    /**
     * {@inheritdoc}
     */
    public static function setUserRatingsModel($model)
    {
        static::$userRatingsModel = $model;
    }

    /**
     * {@inheritdoc}
     */
    public function userRatings()
    {
        $instance = new static;
        return $instance->createUserRatingsModel()
            ->whereNamespace($instance->getUserRatingEntityClassName())
            ->where('entity_id', $this->id);
    }

    /**
     * {@inheritdoc}
     */
    public static function allUserRatings()
    {
        $instance = new static;

        return $instance->createUserRatingsModel()
            ->whereNamespace($instance->getUserRatingEntityClassName());
    }

    /**
     * {@inheritdoc}
     */
    public function addUserRating($userId, $params)
    {
        $userRating = $this->createUserRatingsModel()->firstOrNew([
            'namespace' => $this->getUserRatingEntityClassName(),
            'entity_id' => $this->id,
            'user_id' => $userId,
            'rating' => isset($params['rating']) && !is_null($params['rating']) ? $params['rating'] : null,
            'like' => isset($params['like']) && !is_null($params['like']) ? (int) $params['like'] : null,
            'dislike' => isset($params['dislike']) && !is_null($params['dislike']) ? (int) $params['dislike'] : null,
            'favorite' => isset($params['favorite']) && !is_null($params['favorite']) ? (int) $params['favorite'] : null,
            'comment' => isset($params['comment']) && !empty($params['comment']) ? $params['comment'] : null,
        ]);

        return $userRating;
    }

    /**
     * {@inheritdoc}
     */
    public static function createUserRatingsModel()
    {
        return new static::$userRatingsModel;
    }

    /**
     * @return string
     */
    protected function getUserRatingEntityClassName()
    {
        if (isset(static::$entityNamespace)) {
            return static::$entityNamespace;
        }

        return $this->getEntityClassName();
    }

    /**
     * {@inheritdoc}
     */
    public function deleteUserRating($userId)
    {
        $userRating = $this->createUserRatingsModel()->firstOrNew([
            'namespace' => $this->getUserRatingEntityClassName(),
            'entity_id' => $this->id,
            'user_id' => $userId
        ]);

        if ($userRating->exists) {
            $userRating->delete();
        }

        return true;
    }
}
