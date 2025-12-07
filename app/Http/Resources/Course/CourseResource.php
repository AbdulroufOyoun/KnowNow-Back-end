<?php

namespace App\Http\Resources\Course;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\URL;

class CourseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request)
    {
        if ($this->is_active) {
            return [
                'id' => $this->id,
                'image' => URL::to('Images/Courses', $this->poster),
                'name' => $this->name,
                'description' => $this->description,
                'price' => $this->price,
                'ratio' => $this->ratio,
                'doctor' => $this->Doctor->name,
                'university' => $this->University->name,
            ];
        }
    }
}
