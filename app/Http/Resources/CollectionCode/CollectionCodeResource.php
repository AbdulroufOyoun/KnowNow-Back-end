<?php

namespace App\Http\Resources\CollectionCode;

use App\Http\Resources\Collection\CollectionResource;
use App\Http\Resources\User\UserResource;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CollectionCodeResource extends JsonResource
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
            'created_by' => new UserResource($this->User),
            'collection' => new CollectionResource($this->Collection),
            'expire_at' => Carbon::parse($this->expire_at)->format('Y-m-d'),

        ];
    }
}
