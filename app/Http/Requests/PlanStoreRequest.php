<?php

namespace App\Http\Requests;

use App\Enums\PaymentGatewaySlugEnum;
use App\Rules\ForceFail;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use MoveOn\Common\Traits\Validatable;
use MoveOn\Subscription\Enums\IntervalUnit;
use MoveOn\Subscription\Enums\PricingScheme;
use MoveOn\Subscription\Enums\QuantitySource;
use MoveOn\Subscription\Enums\UsageType;
use MoveOn\Subscription\Models\Product;

class PlanStoreRequest extends FormRequest
{
    use Validatable;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $pricingScheme     = request('pricing_scheme');
        $tieredScheme      = PricingScheme::TIERED();
        $tiers             = request('tiers');
        $totalTierItems    = is_array($tiers) ? count($tiers) : 0;
        $lastTierItemIndex = $totalTierItems > 0 ? $totalTierItems - 1 : 0;
        $product = Product::find($this->get("product_id"));
        $gatewaySlug = $product?->gateway?->slug;
        $availableIntervalUnits = IntervalUnit::values();

        $rules = [
            "product_id"          => ["required", Rule::exists((new Product())->getTable(), "id")],
            "currency"            => ["required", "string", "max:255"],
            "name"                => ["required", "string", "max:255"],
            "unit_amount"         => ["required", "numeric"],
            "interval_unit"       => ["required", "string", "max:255", Rule::in($availableIntervalUnits)],
            "interval_count"      => ["required", "integer"],
            "usage_type"          => ["required", "string", "max:255", Rule::in(UsageType::values())],
            "pricing_scheme"      => ["required", "string", "max:255", Rule::in(PricingScheme::values())],
            "quantity_source"     => ["required", "string", "max:255", Rule::in(QuantitySource::values())],
            "trial_period_days"   => ["required", "integer"],
            "system_usage_charge" => ["required", "numeric"],
            "is_active"           => ["required", "bool"],
            "default_quantity"    => ["required", "integer"],
            "description"         => ["required", "string", "max:255"],
            "tiers"               => ["required_if:$pricingScheme,$tieredScheme", "array", "min:2", $this->isTierItemStartEndSequential($lastTierItemIndex)],
            "tiers.*.start"       => ["required", "integer"],
            "tiers.*.end"         => ["nullable", "integer"],
            "tiers.*.price"       => ["required", "numeric"],
            "tiers.0.start"       => ["required_if:$pricingScheme,$tieredScheme", "numeric", "min:1", "max:1"],
        ];


        if ($totalTierItems > 0 && array_key_exists(
                "end",
                $tiers[$lastTierItemIndex]
            )
            && !empty($tiers[$lastTierItemIndex]["end"])) {
            $rules["tiers.$lastTierItemIndex.end"] = [
                new ForceFail(
                    "tiers.$lastTierItemIndex.end must be null or should not be passed"
                ),
            ];
        }

        return $rules;
    }

    private function isTierItemStartEndSequential(int $lastTierItemIndex): Closure
    {
        return function ($attribute, $tiers, $fail) use ($lastTierItemIndex) {
            $tierStartItems = Arr::pluck($tiers, "start");
            $tierEndItems   = Arr::pluck($tiers, "end");
            $isSequential   = true;

            foreach ($tierEndItems as $index => $end) {
                if ($index == $lastTierItemIndex) break;
                if ($tierStartItems[$index + 1] - $end !== 1) {
                    $isSequential = false;
                    break;
                }
            }

            if (!$isSequential) {
                $fail("tiers start and end range has to be sequential. i.e:(1-2 , 3-4, 5-6)");
            }
        };
    }

    public function messages(): array
    {
        return [
            "tiers.0.start.min" => "The first Tier component start must be 1",
            "tiers.0.start.max" => "The first Tier component start must be 1",
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge(
            [
                "currency"            => "USD",
                "trial_period_days"   => 0,
                "system_usage_charge" => 0,
                "default_quantity"    => 1,
                "usage_type"          => UsageType::LICENCED(),
                "pricing_scheme"      => PricingScheme::FIXED(),
                "quantity_source"     => QuantitySource::DEFAULT(),
            ]
        );
    }
}




















