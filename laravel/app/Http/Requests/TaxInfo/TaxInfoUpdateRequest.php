<?php

namespace App\Http\Requests\TaxInfo;

use App\Http\Requests\BaseRequest;

class TaxInfoUpdateRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'countryId' => ['sometimes', 'required', 'exists:countries,id'],
            'taxRate' => ['sometimes', 'required', 'numeric', 'between:0,100'],
            'description' => ['sometimes', 'nullable', 'string'],
        ];
    }
}
