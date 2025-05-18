<?php

namespace App\Http\Requests\CoworkingSpace;

use App\Http\Requests\BaseRequest;

class CoworkingSpaceUpdateRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'address' => ['sometimes', 'required', 'string', 'max:255'],
            'city' => ['sometimes', 'required', 'string', 'max:255'],
            'countryId' => ['sometimes', 'required', 'exists:countries,id'],
            'hours' => ['sometimes', 'required', 'string', 'max:255'],
            'cost' => ['sometimes', 'required', 'numeric', 'min:0'],
            'wifiSpeed' => ['sometimes', 'nullable', 'string', 'max:255'],
            'hasCoffee' => ['sometimes', 'required', 'boolean'],
            'is24_7' => ['sometimes', 'required', 'boolean'],
            'website' => ['sometimes', 'nullable', 'string', 'max:255'],
        ];
    }
}
