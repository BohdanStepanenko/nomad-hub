<?php

namespace App\Http\Requests\Country;

use App\Http\Requests\BaseRequest;

class CountryStoreRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:countries,name'],
            'code' => ['required', 'string', 'size:2', 'unique:countries,code'],
        ];
    }
}
