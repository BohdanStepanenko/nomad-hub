<?php

namespace App\Http\Requests\FavoriteHousing;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class FavoriteHousingRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'sortBy' => ['nullable', Rule::in(['created_at', 'updated_at'])],
            'sortDirection' => ['nullable', Rule::in(['asc', 'desc'])],
        ];
    }
}
