<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class GetNewsArticleRequest extends FormRequest
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
        $values = ['source','category','author'];
        return [
            'get_through'   => 'required|in:' . implode(',', $values),
            'my_favorite'      => 'required',
        ];
    }

    public function messages()
    {
        return [
            'get_through.in' => 'Please select any value between (source, category, author).',
        ];
    }
}
