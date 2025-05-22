<?php

namespace App\Http\Requests\Search;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class AutocompleteRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'q' => ['required', 'string', 'max:255'],
            'type' => ['nullable', Rule::in(['coworking', 'housing', 'all'])],
        ];
    }
}
