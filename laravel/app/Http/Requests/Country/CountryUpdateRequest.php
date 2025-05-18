<?php

namespace App\Http\Requests\Country;

use App\Http\Requests\BaseRequest;

class CountryUpdateRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:255', 'unique:countries,name,' . $this->country->id],
            'code' => ['sometimes', 'required', 'string', 'size:2', 'unique:countries,code,' . $this->country->id],
        ];
    }
}
