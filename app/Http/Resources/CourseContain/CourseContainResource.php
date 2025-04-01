<?php

namespace App\Http\Resources\CourseContain;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseContainResource extends JsonResource
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
            'name' => $this->name,
            'video' => $this->video,
            'pdf' => $this->pdf,
            'is_free' => $this->is_free,
        ];
    }
}