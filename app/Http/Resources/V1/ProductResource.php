<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "category" => $this->category,
            "description" => $this->description,
            "image_url" => $this->image_url,
            "home_url" => $this->home_url,
            "gateway_id" => $this->gateway_id,
            "gateway_product_id" => $this->gateway_product_id,
            "id" => $this->id,
        ];
    }
}
