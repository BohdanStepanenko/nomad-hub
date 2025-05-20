<?php

namespace App\Http\Requests\Housing;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class HousingRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'sortBy' => ['nullable', Rule::in(['name', 'price', 'created_at', 'updated_at'])],
            'sortDirection' => ['nullable', Rule::in(['asc', 'desc'])],
        ];
    }
}
