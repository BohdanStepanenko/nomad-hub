<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class BaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function failedValidation(Validator $validator)
    {
        $errors = (new ValidationException($validator))->errors();

        throw new HttpResponseException(response()->json([
            'success' => false,
            'errors' => $this->mutationErrors($errors),
        ], Response::HTTP_UNPROCESSABLE_ENTITY));
    }

    private function mutationErrors($errors): array
    {
        return array_map(function ($value) {
            return $value[0];
        }, $errors);
    }
}
