<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MyOrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return array_merge([
            'id' => $this->id,
            'ticket_number' => $this->ticket_number,
            'status' => $this->status,
            'total_price' => $this->total_price,
            'total_paid' => $this->total_paid,
            'total_paid_with_points' => $this->total_paid_with_points,
            'points_gained' => $this->points_gained,
            'points_used_to_pay' => $this->points_used_to_pay,
            'payment_type' => $this->payment_type,
            'payment_reference' => $this->payment_reference,
            'date' => $this->date,
            'cancel_reason' => $this->cancelReason(),
            'all_dishes_ready' => $this->allDishesReady(),
        ], ['items' => OrderItemResource::collection($this->items)]);
    }
}
