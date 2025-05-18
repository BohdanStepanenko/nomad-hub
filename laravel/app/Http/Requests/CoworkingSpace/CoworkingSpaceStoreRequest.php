<?php

namespace App\Http\Requests\CoworkingSpace;

use App\Http\Requests\BaseRequest;

class CoworkingSpaceStoreRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'countryId' => ['required', 'exists:countries,id'],
            'hours' => ['required', 'string', 'max:255'],
            'cost' => ['required', 'numeric', 'min:0'],
            'wifiSpeed' => ['nullable', 'string', 'max:255'],
            'hasCoffee' => ['required', 'boolean'],
            'is24_7' => ['required', 'boolean'],
            'website' => ['nullable', 'string', 'max:255'],
        ];
    }
}
