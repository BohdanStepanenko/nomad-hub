<?php

namespace App\Http\Requests\FavoriteHousing;

use App\Http\Requests\BaseRequest;

class FavoriteHousingUpdateRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'housingId' => ['sometimes', 'required', 'exists:housings,id'],
        ];
    }
}
