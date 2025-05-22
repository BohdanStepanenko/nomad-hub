<?php

namespace App\Http\Requests\Housing;

use App\Http\Requests\BaseRequest;

class HousingUpdateRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
            'city' => ['sometimes', 'required', 'string', 'max:255'],
            'countryId' => ['sometimes', 'required', 'exists:countries,id'],
            'address' => ['sometimes', 'required', 'string', 'max:255'],
            'price' => ['sometimes', 'required', 'numeric', 'min:0'],
        ];
    }
}
