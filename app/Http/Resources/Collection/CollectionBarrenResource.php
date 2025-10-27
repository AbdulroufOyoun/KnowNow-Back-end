<?php

namespace App\Http\Resources\Collection;

use App\Http\Resources\Course\CourseResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CollectionBarrenResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'course'=>new CourseResource($this->Courses),
            'count'=>$this->count,
            'totalMony'=>$this->totalMony,
            'doctorBarren'=>$this->doctorBarren,
            'price'=>$this->price,
        ];
    }
}
