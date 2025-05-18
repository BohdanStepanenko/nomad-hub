<?php

namespace App\Http\Requests\Visa;

use App\Http\Requests\BaseRequest;

class VisaStoreRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'countryId' => ['required', 'exists:countries,id'],
            'visaType' => ['required', 'string', 'max:255'],
            'duration' => ['required', 'integer', 'min:1'],
            'requirements' => ['required', 'string'],
            'cost' => ['required', 'numeric', 'min:0'],
            'source' => ['nullable', 'string'],
        ];
    }
}
