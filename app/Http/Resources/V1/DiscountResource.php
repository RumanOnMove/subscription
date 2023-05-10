<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class DiscountResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            "id"                      => $this->id,
            "name"                    => $this->name,
            "coupon_code"             => $this->coupon_code,
            "currency"                => $this->currency,
            "duration"                => $this->duration,
            "duration_in_months"      => $this->duration_in_months,
            "amount_type"             => $this->amount_type,
            "gateway_coupon_id"       => $this->gateway_coupon_id,
            "amount"                  => $this->amount,
            "maximum_discount_amount" => $this->maximum_discount_amount,
            "is_active"               => $this->is_active,
            "gateway"                 => new PaymentGatewayResource($this->whenLoaded("gateway"))
        ];
    }
}
