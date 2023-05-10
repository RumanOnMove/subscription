<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class PaymentGatewayResource extends JsonResource
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
            "id"               => $this->id,
            "name"             => $this->name,
            "slug"             => $this->slug,
            "gateway_type"     => $this->gateway_type,
            "fee_type"         => $this->fee_type,
            "fee"              => $this->fee,
            "logo"             => $this->logo,
            "url"              => $this->url,
            "display_order"    => $this->display_order,
            "is_active"        => $this->is_active,
            "customer_visible" => $this->customer_visible,
        ];
    }
}
