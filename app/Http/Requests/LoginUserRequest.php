<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class LoginUserRequest extends FormRequest
{
    public function failedValidation(Validator $validator)
    {
        $statusCode = $validator->failed() ? 200 : 200;

        throw new HttpResponseException(response()->json([
            'status' => false,
            'message'=> 'Validation errors',
            'type'   => 'validation',
            'errors' => $validator->errors(),
        ], $statusCode));
    }
    
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email',
            'password' => 'required',
        ];
    }
}
