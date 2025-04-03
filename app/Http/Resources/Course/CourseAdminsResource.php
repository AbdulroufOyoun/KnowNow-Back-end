<?php

namespace App\Http\Resources\Course;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\URL;

class CourseAdminsResource extends JsonResource
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
            'image' => URL::to('Images/Courses', $this->image),
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'doctor' => $this->Doctor->name,
            'university' => $this->University->name,
            'is_active' => $this->is_active,
            'ratio' => $this->ratio
        ];
    }
}