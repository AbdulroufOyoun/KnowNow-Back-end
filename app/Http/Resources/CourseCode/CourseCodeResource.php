<?php

namespace App\Http\Resources\CourseCode;

use App\Http\Resources\Course\CourseResource;
use App\Http\Resources\User\StudentResource;
use App\Http\Resources\User\UserResource;
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
            'is_free' =>  $this->is_free,
            'price' =>  $this->price,
            'created_by' => new UserResource($this->User),
            // 'course' => new CourseResource($this->Course),
            'user' => new StudentResource($this->User),
            'expire_at' => Carbon::parse($this->expire_at)->format('Y-m-d'),
            'created_at' =>  Carbon::parse($this->created_at)->format('Y-m-d'),

        ];
    }
}
