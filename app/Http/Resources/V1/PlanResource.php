<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;
use MoveOn\Subscription\Models\Discount;
use MoveOn\Subscription\Modules\PlanDiscountModule;

class PlanResource extends JsonResource
{
    protected $discount;

    public function __construct($resource, $discount = null)
    {
        $this->discount = $discount instanceof Discount ? $discount : null;

        parent::__construct($resource);
    }

    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $discountDetails = !empty($this->discount)
            ? PlanDiscountModule::make()->gePlanAmount($this->resource, $this->discount)
            : PlanDiscountModule::make()->gePlanAmount($this->resource);

        return [
            "id"                  => $this->id,
            "gateway_id"          => $this->gateway_id,
            "product_id"          => $this->product_id,
            "gateway_plan_id"     => $this->gateway_plan_id,
            "currency"            => $this->currency,
            "name"                => $this->name,
            "description"         => $this->description,
            "unit_amount"         => $this->unit_amount,
            "quantity_source"     => $this->quantity_source,
            "default_quantity"    => $this->default_quantity,
            "usage_type"          => $this->usage_type,
            "interval_unit"       => $this->interval_unit,
            "interval_count"      => $this->interval_count,
            "pricing_scheme"      => $this->pricing_scheme,
            "trial_period_days"   => $this->trial_period_days,
            "system_usage_charge" => $this->system_usage_charge,
            "is_active"           => $this->is_active,
            "is_primary"          => $this->pivot?->is_primary ?? $this->is_primary,
            "is_trial"            => $this->pivot?->is_trial ?? $this->is_trial,
            "plan_group_id"       => $this->plan_group_id,
            "created_at"          => $this->created_at,
            "updated_at"          => $this->updated_at,
            "gateway"             => new PaymentGatewayResource($this->whenLoaded("gateway")),
            "amount_details"      => [
                "sub_total" => $discountDetails[0],
                "discount"  => $discountDetails[1],
                "total"     => $discountDetails[2],
            ],
        ];
    }
}
