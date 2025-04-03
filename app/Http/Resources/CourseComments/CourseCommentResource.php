<?php

namespace App\Http\Resources\CourseComments;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseCommentResource extends JsonResource
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
            'comment' => ($this->comment) ? $this->comment : $this->sub_comment,
            'user' => $this->User->name,
            'created_at' => Carbon::parse($this->created_at)->format('Y-m-d g:m A'),

            'subComments' => CourseCommentResource::collection($this->subComments)
        ];
    }
}
