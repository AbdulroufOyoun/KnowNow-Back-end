<?php

namespace App\Http\Resources\Collection;

use App\Http\Resources\Course\CourseResource;
use App\Models\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CollectionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $courses = $this->Courses;
        $oldPrice = 0;
        foreach ($courses as $course) {
            $oldPrice += $course->price;
        }
        return [
            'id' => $this->id,
            'name' => $this->name,
            'price' => $this->price,
            'oldPrice' => $oldPrice,
            'courses' => CourseResource::collection($courses)
        ];
    }
}