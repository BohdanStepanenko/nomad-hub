<?php

namespace App\Http\Requests\Visa;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class VisaRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'sortBy' => ['nullable', Rule::in(['visaType', 'duration', 'cost'])],
            'sortDirection' => ['nullable', Rule::in(['asc', 'desc'])],
        ];
    }
}
