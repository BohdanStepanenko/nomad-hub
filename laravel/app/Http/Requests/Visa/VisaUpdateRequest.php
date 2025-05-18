<?php

namespace App\Http\Requests\Visa;

use App\Http\Requests\BaseRequest;

class VisaUpdateRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'countryId' => ['sometimes', 'required', 'integer', 'exists:countries,id'],
            'visaType' => ['sometimes', 'required', 'string', 'max:255'],
            'duration' => ['sometimes', 'required', 'integer', 'min:1'],
            'requirements' => ['sometimes', 'required', 'string'],
            'cost' => ['sometimes', 'required', 'numeric', 'min:0'],
            'source' => ['sometimes', 'nullable', 'string'],
        ];
    }
}
