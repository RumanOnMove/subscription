<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\PaymentGatewaySlugEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\SubscriptionStoreRequest;
use App\Http\Requests\UpdateSubscriptionRequest;
use App\Http\Resources\V1\SubscriptionResource;
use App\Models\User;
use App\Models\UserTrialPlanGroup;
use App\Modules\Subscription\SubscriptionModule;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use MoveOn\Subscription\Enums\SubscriptionStatus;
use MoveOn\Subscription\Models\Discount;
use MoveOn\Subscription\Models\PaymentGateway;
use MoveOn\Subscription\Models\Plan;
use MoveOn\Subscription\Models\Subscription;
use MoveOn\Subscription\Requests\CustomerStoreDTORequest;
use MoveOn\Subscription\Requests\SubscriptionCreateDTORequest;
use MoveOn\Subscription\Service\GatewayAssociatedCustomerService;
use MoveOn\Subscription\Service\SubscriptionService;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class SubscriptionController extends Controller
{
    /**
     * List of subscription
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request): mixed
    {
        $list = SubscriptionService::make()->listSubscription($request);

        return SubscriptionResource::collection($list["result"])->additional(
            [
                "filters" => $list["filters"],
            ]
        )->response()->getData();
    }

    /**
     * Create subscription
     * @param SubscriptionStoreRequest $request
     * @return SubscriptionResource
     */
    public function store(SubscriptionStoreRequest $request): SubscriptionResource
    {
        $subscriptionRes = DB::transaction(function () use ($request) {
            $form               = $request->validated();
            $plan               = Plan::findOrFail($form["plan_id"]);
            $form["owner_id"]   = $request->get('user_id');
            $form["owner_type"] = (new User())->getMorphClass();
            $user               = User::findOrFail($request->get('user_id'));

            abort_unless(
                empty(SubscriptionModule::make()->userCurrentSubscription($user)),
                422,
                'User currently subscribed to a plan'
            );

            $incompleteSubscription = SubscriptionModule::make()->incompleteSubscription($user);

            abort_unless(
                empty($incompleteSubscription),
                422,
                "You already subscribed to a plan that needs payment. You can create new subscription after 1 min."
            );

            # TODO: need discussion
//            $trialStatus = SubscriptionModule::make()
//                ->userCanCreateTrialPlan($form["owner_id"], $plan->id)
//            ;
//
//            abort_unless(
//                $trialStatus["can_progress"],
//                422,
//                "You already trialed this plan."
//            );


//            $form["cancel_at_period_end"] = (bool)$trialStatus["is_trial_plan"];

            $form["gateway_id"] = $plan->gateway_id;
            $form["gateway"]    = PaymentGateway::findOrFail($plan->gateway_id);
            $form["plan"]       = $plan;
            $form["quantity"]   = $form["plan"]->default_quantity;

            $request = new Request([
                "ownerable_id"   => $request->get('user_id'),
                "ownerable_type" => (new User())->getMorphClass(),
                "gateway_id"     => $form["gateway_id"]
            ]);

            $existingCustomers = GatewayAssociatedCustomerService::make()->listCustomer($request);

            if ($existingCustomers["result"]->count() > 0) {
                $customer = $existingCustomers["result"]->first();
            } else {
                $customer = GatewayAssociatedCustomerService::make()->createCustomer(
                    $form["gateway"],
                    new CustomerStoreDTORequest(...[
                        "ownerable_id"   => $request->get('ownerable_id'),
                        "ownerable_type" => (new User())->getMorphClass(),
                        "email"          => $user->email,
                        "name"           => $user->name,
                    ])
                );
            }
            $form["customer"] = $customer;
            if (isset($form["coupon"])) {
                $form["discount"] = Discount::where("coupon_code", $form["coupon"])->first();
                unset($form["coupon"]);
            }

            unset($form["gateway_id"], $form["plan_id"], $form["customer_id"], $form["discount_id"]);
            $subscriptionRes = SubscriptionService::make()->createSubscription(
                new SubscriptionCreateDTORequest(...$form)
            );

            // TODO: Have to discuss
//            if ($trialStatus["is_trial_plan"] && $trialStatus["plan_group_id"]) {
//                UserTrialPlanGroup::create([
//                    "user_id"       => $form["owner_id"],
//                    "plan_group_id" => $trialStatus["plan_group_id"],
//                    "plan_id"       => $plan->id
//                ]);
//            }

            return $subscriptionRes;
        });

        if ($subscriptionRes instanceof SubscriptionResource) {
            return $subscriptionRes;
        }

        $status       = $subscriptionRes["subscription"]["status"];
        $shouldReload = ($status === SubscriptionStatus::PAYPAL_ACTIVE() || $status === SubscriptionStatus::STRIPE_ACTIVE());
        return (new SubscriptionResource($subscriptionRes["subscription"]))
            ->additional([
                "success"              => true,
                "gateway_slug"         => $subscriptionRes["gateway_slug"],
                "gateway_subscription" => $subscriptionRes["gateway_subscription"],
                "should_reload"        => $shouldReload,
                "message"              => "Subscription created successfully"
            ])
            ;
    }

    /**
     * @throws Exception
     */
    public function update(UpdateSubscriptionRequest $request, Subscription $subscription): JsonResponse
    {
        if ($request->action == "cancel") {
            if (SubscriptionService::make()->cancelSubscription($subscription)) {
                return response()->json([
                    "success" => true,
                    "message" => "Subscription {$request->action} action success"
                ], ResponseAlias::HTTP_OK);
            }

            return response()->json([
                "success" => false,
                "message" => "Subscription {$request->action} action failed"
            ], ResponseAlias::HTTP_UNPROCESSABLE_ENTITY);
        }


        return response()->json([
            "success" => false,
            "message" => "Unknown action given"
        ], ResponseAlias::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function cancelSubscription() {

    }
}
