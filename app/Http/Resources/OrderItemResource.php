<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $prod = $this->product()->withTrashed()->get()[0];
        return [
            'id' => $this->id,
            'order_local_number' => $this->order_local_number,
            'product' => [
                'name' => $prod->name,
                'photo_url' => $prod->photo_url,
                'type' => $prod->type,
                'deleted' => ($prod->deleted_at ? true : false),
            ],
            'status' => $this->status,
            'price' => $this->price,
            'preparation_by' => ($this->preparation_by ? $this->preparation()->withTrashed()->get()[0]->name : null),
            'notes' => $this->notes,
        ];
    }
}
