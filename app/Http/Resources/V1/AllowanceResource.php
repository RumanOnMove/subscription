<?php

namespace App\Http\Resources\V1;

use App\Enums\AllowanceTypeEnum;
use Illuminate\Http\Resources\Json\JsonResource;

class AllowanceResource extends JsonResource
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
            "id"      => $this->id,
            "name"    => $this->name,
            "shop_id" => $this->shop_id,
            "shop"    => new ShopResource($this->whenLoaded('shop')),
            "meta" => $this->meta,
            $this->mergeWhen($this->name == AllowanceTypeEnum::SHOP_ALLOWANCE(), [
                "shopAllowance" => new AllowanceDetailsResource($this->whenLoaded("unlinkedAllowance")),
            ]),
            $this->mergeWhen($this->name == AllowanceTypeEnum::STORE_ALLOWANCE(), [
                "storeAllowances" => AllowanceDetailsResource::collection($this->whenLoaded("storeRelatedAllowances")),
            ]),
            $this->mergeWhen($this->name == AllowanceTypeEnum::VIDEO_ALLOWANCE(), [
                "videoAllowances" => AllowanceDetailsResource::collection($this->whenLoaded("storeRelatedAllowances")),
            ]),
            $this->mergeWhen($this->name == AllowanceTypeEnum::ORDER_FULFILMENT_ALLOWANCE(), [
                "orderFulfilmentAllowances" => AllowanceDetailsResource::collection($this->whenLoaded("storeRelatedAllowances")),
            ]),
        ];
    }
}
