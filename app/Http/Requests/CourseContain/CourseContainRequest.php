<?php

namespace App\Http\Requests\CourseContain;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class CourseContainRequest extends FormRequest
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
            'video' => 'required|file|mimes:mp4,mov,avi,mkv',
            'pdf' => 'nullable|file|mimes:pdf',
            'course_id' => 'required',
            'is_free' => 'required',
            'is_theoretical' => 'required'
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
