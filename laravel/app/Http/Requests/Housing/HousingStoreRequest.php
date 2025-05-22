<?php

namespace App\Http\Requests\Housing;

use App\Http\Requests\BaseRequest;

class HousingStoreRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'city' => ['required', 'string', 'max:255'],
            'countryId' => ['required', 'exists:countries,id'],
            'address' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
        ];
    }
}
