<?php

namespace App\Http\Requests\SpecializationCourse;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SpecializationCourseRequest extends FormRequest
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
                        'specialization_id' => [Rule::exists('specializations', 'id'), 'required'],
                        'course_id' => [Rule::exists('courses', 'id'), 'required'],
                                 'year'=>'required|integer|between:1,5',
            'chapter'=>'required|integer|between:1,2',

        ];
    }
}
