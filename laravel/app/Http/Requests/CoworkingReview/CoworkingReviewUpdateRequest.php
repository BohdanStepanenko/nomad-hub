<?php

namespace App\Http\Requests\CoworkingReview;

use App\Http\Requests\BaseRequest;

class CoworkingReviewUpdateRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'coworkingSpaceId' => ['sometimes', 'required', 'exists:coworking_spaces,id'],
            'userId' => ['sometimes', 'required', 'exists:users,id'],
            'rating' => ['sometimes', 'required', 'integer', 'min:1', 'max:5'],
            'comment' => ['sometimes', 'nullable', 'string'],
        ];
    }
}
