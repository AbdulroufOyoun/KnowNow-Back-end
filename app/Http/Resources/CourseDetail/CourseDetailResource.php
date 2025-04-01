<?php

namespace App\Http\Resources\CourseDetail;

use App\Http\Resources\CourseDescription\CourseDescriptionResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseDetailResource extends JsonResource
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
            'title' => $this->title,
            'descriptions' => CourseDescriptionResource::collection($this->Descriptions)
        ];
    }
}
