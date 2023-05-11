<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\PaymentGatewaySlugEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\StorePlanRequest;
use App\Http\Resources\V1\PlanResource;
use App\Models\PlanGroupPlan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use MoveOn\Subscription\Collection\Modules\Paypal\PlanTierCollection;
use MoveOn\Subscription\Enums\IntervalUnit;
use MoveOn\Subscription\Enums\PricingScheme;
use MoveOn\Subscription\Enums\QuantitySource;
use MoveOn\Subscription\Enums\UsageType;
use MoveOn\Subscription\Models\Plan;
use MoveOn\Subscription\Models\Product;
use MoveOn\Subscription\Requests\PlanCreateDTORequest;
use MoveOn\Subscription\Requests\PlanTierDTORequest;
use MoveOn\Subscription\Service\PlanService;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class PlanController extends Controller
{
    /**
     * List of plans
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $plans = PlanService::make()->listPlans($request);

        $plans["result"] = $plans["result"]->map(function ($plan) {
            $planGroupPlan = PlanGroupPlan::where("plan_id", $plan->id)->first();
            if ($planGroupPlan) {
                $plan->is_primary = $planGroupPlan->is_primary;
                $plan->is_trial = $planGroupPlan->is_trial;
            }
            return $plan;
        });

        return PlanResource::collection($plans["result"])->additional(
            [
                "filters" => $plans["filters"],
                "success" => true,
                "message" => "Plan fetched successfully",
            ]
        );
    }

    /**
     * Show specific plan
     * @param Plan $plan
     * @return PlanResource|JsonResponse
     */
    public function show(Plan $plan): PlanResource|JsonResponse
    {
        if (!$plan->is_active) {
            return response()->json([
                "success" => false,
                "message" => "Plan not found",
            ], ResponseAlias::HTTP_UNPROCESSABLE_ENTITY);
        }

        $planGroupPlan = PlanGroupPlan::where("plan_id", $plan->id)->first();
        if ($planGroupPlan) {
            $plan->is_primary = $planGroupPlan->is_primary;
            $plan->is_trial   = $planGroupPlan->is_trial;
        }
        return (new PlanResource($plan))->additional(
            [
                "success" => true,
                "message" => "Plan fetched successfully",
            ]
        );
    }

    /**
     * Store new plan
     * @param StorePlanRequest $request
     * @return PlanResource
     */
    public function store(StorePlanRequest $request,): PlanResource
    {
        $form = $request->validated();
        $product = Product::with("gateway")->findOrFail($form["product_id"]);

        abort_unless($product?->gateway?->slug, 422, "Product gateway does not have slug");

        if($product?->gateway?->slug  && $product?->gateway?->slug == PaymentGatewaySlugEnum::SHOPIFY()){
            $plan = Plan::create(array_merge(
                $form,
                [
                    "gateway_id" => $product->gateway->id
                ]
            ));
            return (new PlanResource($plan))->additional(
                [
                    "success" => true,
                    "message" => "Plan created successfully",
                ]
            );
        }


        $tierCollection = PlanTierCollection::make();

        if (isset($form["tiers"])) {
            $tiers          = $form["tiers"];
            foreach ($tiers as $tier) {
                $tierCollection = $tierCollection->add(
                    new PlanTierDTORequest(...$tier)
                );
            }
        }

        $form["tiers"]           = $tierCollection;
        $form["product"]         = Product::findOrFail($form["product_id"]);
        $form["interval_unit"]   = IntervalUnit::from($form["interval_unit"]);
        $form["usage_type"]      = UsageType::from($form["usage_type"]);
        $form["pricing_scheme"]  = PricingScheme::from($form["pricing_scheme"]);
        $form["quantity_source"] = QuantitySource::from($form["quantity_source"]);
        unset($form["product_id"]);
        $plan = PlanService::make()->createPlan(
            new PlanCreateDTORequest(...$form),
        );
        return (new PlanResource($plan))->additional(
            [
                "success" => true,
                "message" => "Plan created successfully",
            ]
        );
    }

    /**
     * Plan activation
     * @param Plan $plan
     * @return JsonResponse
     */
    public function activate(Plan $plan): JsonResponse
    {
        if (PlanService::make()->activate($plan)) {
            return response()->json([
                "success" => true,
                "message" => "Plan activated successfully",
            ], ResponseAlias::HTTP_OK);
        }

        return response()->json([
            "success" => false,
            "message" => "Plan activation failed",
        ], ResponseAlias::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * Plan Deactivation
     * @param Plan $plan
     * @return JsonResponse
     */
    public function deactivate(Plan $plan): JsonResponse
    {
        if (PlanService::make()->deactivate($plan)) {
            return response()->json([
                "success" => true,
                "message" => "Plan deactivated successfully",
            ], ResponseAlias::HTTP_OK);
        }

        return response()->json([
            "success" => false,
            "message" => "Plan deactivation failed",
        ], ResponseAlias::HTTP_UNPROCESSABLE_ENTITY);
    }
}
