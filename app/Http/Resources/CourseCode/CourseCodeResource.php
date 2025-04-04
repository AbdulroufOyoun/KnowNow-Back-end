<?php

namespace App\Http\Resources\CourseCode;

use App\Http\Resources\Course\CourseResource;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseCodeResource extends JsonResource
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
            'code' =>  $this->code,
            'created_by' => $this->created_by,
            'course' => new CourseResource($this->Course),
            'expire_at' => Carbon::parse($this->expire_at)->format('Y-m-d'),

        ];
    }
}
