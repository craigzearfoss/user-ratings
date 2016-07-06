<?php

namespace Craigzearfoss\UserRatings;

use App\Http\Requests\Request;

class UserRatingRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'entity_id' => 'integer',
            'dislike' => 'boolean',
            'favorite' => 'boolean',
            'like' => 'boolean',
            'namespace' => 'required',
            'score' => 'numeric',
            'user_id' => 'integer'
        ];

        if (!empty($this->user_rating->id)) {
            // update
        }

        return $rules;
    }
}
