<?php

namespace App\Http\Requests\Search;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class SearchRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'q' => ['required', 'string', 'max:255'],
            'type' => ['nullable', Rule::in(['coworking', 'housing', 'all'])],
            'country_id' => ['nullable', 'exists:countries,id'],
            'city' => ['nullable', 'string', 'max:255'],
            'cost_max' => ['nullable', 'numeric', 'min:0'],
            'has_coffee' => ['nullable', 'boolean'],
        ];
    }
}
