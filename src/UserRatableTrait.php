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
    public function addUserRating($comment)
    {
        $userRating = $this->createUserRatingsModel()->firstOrNew([
            'namespace' => $this->getUserRatingEntityClassName(),
            'user_ratable_id' => $this->id,
            'comment' => $comment
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
     * @param array $userRatings
     * @return bool
     */
    public function syncUserRatings($newUserRatings = [])
    {
        // determine if any user ratings should be deleted
        $currentUserRatings = $this->userRatings()->lists('comment', 'id')->toArray();
        $userRatingsToDelete = array_udiff($currentUserRatings, $newUserRatings, 'strcasecmp');
        $newUserRatings = array_udiff($newUserRatings, $currentUserRatings, 'strcasecmp');

        // add the new user ratings
        foreach ($newUserRatings as $comment) {
            $this->addUserRating($comment);
        }

        // delete old user ratings
        foreach ($userRatingsToDelete as $comment) {
            $this->deleteUserRating($comment);
        }

        return true;
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
