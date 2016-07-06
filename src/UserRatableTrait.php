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

    protected static $defaultMaxScore = 100;

    protected static $defaultMinScore = 0;

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
            'score' => isset($params['score']) && !is_null($params['score']) ? $params['score'] : null,
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

    public function getUserRating($userId)
    {
        $instance = new static;
        return $instance->createUserRatingsModel()
            ->whereNamespace($instance->getUserRatingEntityClassName())
            ->where('entity_id', $this->id)
            ->where('user_id', $userId)->get();
    }

    public function insertOrUpdateUserRating($userId, $params)
    {
        $userRating = $this->getUserRating($userId);

        if ($userRating->isEmpty()) {

            $userRating = $this->createUserRatingsModel()
                ->create(
                    array_merge(
                        [
                            'namespace' => $this->getUserRatingEntityClassName(),
                            'entity_id' => $this->id,
                            'user_id' => $userId
                        ],
                        $params
                    )
                );
        } else {

            $userRating = $this->createUserRatingsModel()
                ->where('namespace', $this->getUserRatingEntityClassName())
                ->where('entity_id', $this->id)
                ->where('user_id', $userId)
                ->update($params);
        }

        return $userRating;
    }

    public function dislike($userId)
    {
        return $this->insertOrUpdateUserRating($userId, ['dislike' => 1, 'like' => 0]);
    }

    public function favorite($userId)
    {
        return $this->insertOrUpdateUserRating($userId, ['favorite' => 1]);
    }

    public function isValidScore($score)
    {
        if (!is_numeric($score)) {
            return false;
        }

        if (($score > static::$defaultMaxScore) || ($score < static::$defaultMinScore)) {
            return false;
        }

        return true;
    }

    public function like($userId)
    {
        return $this->insertOrUpdateUserRating($userId, ['like' => 1, 'dislike' => 0]);
    }

    public function score($userId, $score)
    {
        // validate the score before updating
        if (!$this->isValidScore($score)) {
            return false;
        }

        return $this->insertOrUpdateUserRating($userId, ['score' => $score]);
    }

    public function unDislike($userId)
    {
        return $this->insertOrUpdateUserRating($userId, ['dislike' => 0]);
    }

    public function unFavorite($userId)
    {
        return $this->insertOrUpdateUserRating($userId, ['favorite' => 0]);
    }

    public function unLike($userId)
    {
        return $this->insertOrUpdateUserRating($userId, ['like' => 0]);
    }

    public function unScore($userId)
    {
        return $this->insertOrUpdateUserRating($userId, ['score' => null]);
    }

}
