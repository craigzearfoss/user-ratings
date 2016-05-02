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
     * Get the user ratings associated with the given strain.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function userRatings()
    {
        return $this->belongsToMany(UserRating::class)->withPivot('rating', 'like', 'dislike', 'favorite', 'comment')->withTimestamps();
    }

    /**
     * Get a list of user rating ids associated with the current strain.
     *
     * @return array
     */
    public function getUserRatingListAttribute()
    {
        return $this->userRatings->lists('id');
    }


    /**
     * {@inheritdoc}
     */
    public function userRatings()
    {
        $instance = new static;
        return $instance->createUserRatingsModel()
            ->whereNamespace($instance->getUserRatingEntityClassName())
            ->where('user_ratable_id', $this->id);
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
    public function setUserRatings($userRatings, $type = 'comment')
    {
        // Get the current entity user ratings
        $entityUserRatings = $this->userRatings->lists($type)->all();

        // Prepare the user ratings to be added and removed
        $userRatingsToAdd = array_diff($userRatings, $entityUserRatings);
        $userRatingsToDelete = array_diff($entityUserRatings, $userRatings);

        // Delete the user ratings
        if (!empty($userRatingsToDelete)) {
            foreach($userRatingsToDelete as $comment) {
                $this->deleteUserRating($comment);
            }
        }

        // Add the user ratings
        if (!empty($userRatingsToAdd)) {
            foreach ($userRatingsToAdd as $comment) {
                $this->addUserRating($comment);
            }
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function addUserRating($userId, $params)
    {
        $userRating = $this->createUserRatingsModel()->firstOrNew([
            'namespace' => $this->getUserRatingEntityClassName(),
            'user_ratable_id' => $this->id,
            'user_id' => $userId,
            'rating' => isset($params['rating']) && !is_null($params['rating']) ? $params['rating'] : null,
            'like' => isset($params['like']) && !is_null($params['like']) ? (int) $params['like'] : null,
            'dislike' => isset($params['dislike']) && !is_null($params['dislike']) ? (int) $params['dislike'] : null,
            'favorite' => isset($params['favorite']) && !is_null($params['favorite']) ? (int) $params['favorite'] : null,
            'comment' => isset($params['comment']) && !empty($params['comment']) ? $params['comment'] : null,
        ]);

        if (! $userRating->exists) {

            // increment sequence
            $maxSequence = $this->createUserRatingsModel()
                ->whereNamespace($this->getUserRatingEntityClassName())
                ->where(function ($query) {
                    $query
                        ->Where('user_ratable_id', $this->id)
                    ;
                })
                ->max('sequence');
            $userRating->sequence = $maxSequence + 1;

            $userRating->save();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeUserRating($name)
    {
        $namespace = $this->getUserRatingEntityClassName();

        $userRating = $this
            ->createUserRatingsModel()
            ->whereNamespace($namespace)
            ->where(function ($query) use ($name) {
                $query
                    ->orWhere('comment', $name)
                ;
            })
            ->first()
        ;

        if ($userRating) {
            $userRating->update(['count' => $userRating->count - 1]);

            $this->userRatings()->detach($userRating);
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function createUserRatingsModel()
    {
        return new static::$userRatingsModel;
    }

    /**
     * Returns the entity class name.
     *
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
    public function deleteUserRating($comment)
    {
        $userRating = $this->createUserRatingsModel()->firstOrNew([
            'namespace' => $this->getUserRatingEntityClassName(),
            'user_ratable_id' => $this->id,
            'comment' => $comment
        ]);

        if ($userRating->exists) {
            $userRating->delete();
        }

        return true;
    }
}
