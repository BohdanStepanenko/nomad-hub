<?php

namespace App\Http\Requests\CoworkingReview;

use App\Http\Requests\BaseRequest;

class CoworkingReviewStoreRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'coworkingSpaceId' => ['required', 'exists:coworking_spaces,id'],
            'userId' => ['required', 'exists:users,id'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string'],
        ];
    }
}
