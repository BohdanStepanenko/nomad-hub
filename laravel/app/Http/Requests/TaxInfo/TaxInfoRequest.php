<?php

namespace App\Http\Requests\TaxInfo;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class TaxInfoRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'sortBy' => ['nullable', Rule::in(['country_id', 'tax_rate', 'created_at', 'updated_at'])],
            'sortDirection' => ['nullable', Rule::in(['asc', 'desc'])],
        ];
    }
}
