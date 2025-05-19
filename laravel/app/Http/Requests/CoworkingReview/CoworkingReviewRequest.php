<?php

namespace App\Http\Requests\CoworkingReview;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class CoworkingReviewRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'sortBy' => ['nullable', Rule::in(['rating', 'created_at'])],
            'sortDirection' => ['nullable', Rule::in(['asc', 'desc'])],
        ];
    }
}
