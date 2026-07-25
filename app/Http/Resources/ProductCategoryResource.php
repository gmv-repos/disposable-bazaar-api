<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductCategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'parent_id' => $this->parent_id,
            'name' => $this->name,
            'serial_no' => $this->serial_no,
            'slug' => $this->slug,
            'note' => $this->note,
            'image' => $this->category_icon,
            'subCategories' => self::collection($this->childCategories),
            'categorySeoMetadata' => $this->categorySeoMetadata,
        ];
    }
}