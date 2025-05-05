<?php

namespace App\Http\Requests\User;

use App\Http\Requests\BaseRequest;

class UploadAvatarRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'avatar' => ['required', 'file', 'mimes:jpg,png,jpeg', 'max:10240'],
        ];
    }
}
