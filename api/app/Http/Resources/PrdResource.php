<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Prd */
class PrdResource extends JsonResource
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
            'project_id' => $this->project_id,
            'module_id' => $this->module_id,
            'title' => $this->title,
            'content' => $this->content,
            'version' => $this->version,
            'status' => $this->status->value,
            'origin' => $this->origin->value,
            'specs' => SpecResource::collection($this->whenLoaded('specs')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
