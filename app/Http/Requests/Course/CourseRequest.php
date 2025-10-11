<?php

namespace App\Http\Requests\Course;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class CourseRequest extends FormRequest
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
            'name' => 'required',
            'description' => 'required',
            'price' => 'required',
            'university_id' => [Rule::exists('universities', 'id'), 'required'],
            'is_active' => 'required',
            'poster' => 'required',
            'ratio' => 'required|numeric|max:100',
            'doctor_id' => [Rule::exists('users', 'id'), 'required'],
            'year'=>'required|integer|between:1,5',
            'chapter'=>'required|integer|between:1,2',
            'specialization_id' => [Rule::exists('specializations', 'id'), 'required'],

        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $response = response()->json([
            'success' => false,
            'message' => $validator->errors()->first(),
            'code' => 422,
            'data' => null,
        ], 422);
        throw new HttpResponseException($response);
    }
}
