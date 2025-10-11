<?php

namespace App\Http\Resources\Specialization;

use App\Http\Resources\Course\CourseResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class YearCoursesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'chapter' => $this->chapter,
            'year' => $this->year,
            'course' => CourseResource::make($this->Course),
        ];
    }
}