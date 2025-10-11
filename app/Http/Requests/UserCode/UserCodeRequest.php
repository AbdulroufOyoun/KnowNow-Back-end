<?php

namespace App\Http\Requests\UserCode;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class UserCodeRequest extends FormRequest
{
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
            'collection_code' => [Rule::exists('collection_codes', 'code'), 'nullable'],
            'course_code' => [Rule::exists('course_codes', 'code'), 'nullable'],
            'item_id' =>  'required',
        ];
    }
    protected function failedValidation(Validator $validator)
    {
        $response = response()->json([
            'success' => false,
            'message' =>  $validator->errors()->first(),
            'code' => 422,
            'data' => null,
        ], 422);
        throw new HttpResponseException($response);
    }
}
