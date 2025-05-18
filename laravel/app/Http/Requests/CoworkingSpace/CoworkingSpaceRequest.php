<?php

namespace App\Http\Requests\CoworkingSpace;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class CoworkingSpaceRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'sortBy' => ['nullable', Rule::in(['name', 'city', 'cost'])],
            'sortDirection' => ['nullable', Rule::in(['asc', 'desc'])],
        ];
    }
}
