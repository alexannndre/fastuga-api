<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            "product_id" => $this->id,
            "name" => $this->name,
            "description" => $this->description,
            "type" => $this->type,
            "price" => $this->price,
            "photo_url" => $this->photo_url,
            /*"created_at" => $this->created_at,
            "updated_at" => $this->updated_at,*/
            //TODO: display deleted products?
            /*"deleted_at" => $this->deleted_at,*/
        ];
    }
}
