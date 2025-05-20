<?php

namespace App\Http\Requests\TaxInfo;

use App\Http\Requests\BaseRequest;

class TaxInfoStoreRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'countryId' => ['required', 'exists:countries,id'],
            'taxRate' => ['required', 'numeric', 'between:0,100'],
            'description' => ['nullable', 'string'],
        ];
    }
}
