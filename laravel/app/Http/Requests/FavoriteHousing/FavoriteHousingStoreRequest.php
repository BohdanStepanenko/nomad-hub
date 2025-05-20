<?php

namespace App\Http\Requests\FavoriteHousing;

use App\Http\Requests\BaseRequest;

class FavoriteHousingStoreRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'housingId' => ['required', 'exists:housings,id'],
        ];
    }
}
