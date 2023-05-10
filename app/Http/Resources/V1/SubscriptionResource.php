<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionResource extends JsonResource
{
    public array  $allowances;
    public string $validityStatus;

    public array $usage;

    /**
     * @param $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            "id"                      => $this->id,
            "plan_id"                 => $this->plan_id,
            "gateway_id"              => $this->gateway_id,
            "discount_id"             => $this->discount_id,
            "gateway_subscription_id" => $this->gateway_subscription_id,
            "status"                  => $this->status,
            "cancel_reason"           => $this->cancel_reason,
            "gateway"                 => new PaymentGatewayResource($this->whenLoaded("gateway")),
            "plan"                    => new PlanResource($this->whenLoaded("plan")),
            "discount"                => new DiscountResource($this->whenLoaded("discount")),
            'allowances'              => $this->allowances ?? [],
            'validity_status'         => $this->validityStatus ?? null,
            "created_at"              => $this->created_at,
        ];
    }

    public static function new($resource, array $allowances, string $validityStatus)
    : static {
        $res                 = new static($resource);
        $res->allowances     = $allowances;
        $res->validityStatus = $validityStatus;
        return $res;
    }
}
