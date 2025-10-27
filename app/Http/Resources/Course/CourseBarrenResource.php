<?php

namespace App\Http\Resources\Course;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseBarrenResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name'=>$this->name,
            'price'=>$this->price,
            'ratio'=>$this->ratio,
            'university'=>$this->University->name,
            'count'=>$this->count,
            'totalMony'=>$this->totalMony,
            'doctorBarren'=>$this->doctorBarren,
        ];
    }
}
