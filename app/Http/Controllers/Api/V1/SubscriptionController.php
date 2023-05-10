<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\PaymentGatewaySlugEnum;
use App\Enums\ShopSlugEnum;
use App\Enums\UserSubscriptionStatusEnum;
use App\Enums\WebsiteStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSubscriptionRequest;
use App\Http\Requests\UpdateSubscriptionRequest;
use App\Http\Requests\UpDownSubscriptionRequest;
use App\Http\Resources\V1\AllowanceResource;
use App\Http\Resources\V1\RoleResource;
use App\Http\Resources\V1\SubscriptionResource;
use App\Models\PlanGroupPlan;
use App\Models\User;
use App\Models\UserTrialPlanGroup;
use App\Modules\Subscription\SubscriptionModule;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use MoveOn\Subscription\Enums\SubscriptionMeta;
use MoveOn\Subscription\Enums\SubscriptionStatus;
use MoveOn\Subscription\Models\Discount;
use MoveOn\Subscription\Models\PaymentGateway;
use MoveOn\Subscription\Models\Plan;
use MoveOn\Subscription\Models\Subscription;
use MoveOn\Subscription\Requests\CustomerStoreDTORequest;
use MoveOn\Subscription\Requests\SubscriptionCreateDTORequest;
use MoveOn\Subscription\Response\Gateway\Paypal\PaypalErrorResponse;
use MoveOn\Subscription\Service\GatewayAssociatedCustomerService;
use MoveOn\Subscription\Service\SubscriptionService;
use Stripe\Exception\ApiErrorException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class SubscriptionController extends Controller
{
    /**
     * List Of Subscriptions
     * @throws AuthorizationException
     */
    public function index(Request $request)
    {
        $this->authorize("viewAny", Subscription::class);

        $list = SubscriptionService::make()->listSubscription($request);

        return SubscriptionResource::collection($list["result"])->additional(
            [
                "filters" => $list["filters"],
            ]
        )->response()->getData();
    }

    /**
     * Create Subscription
     * @param StoreSubscriptionRequest $request
     * @return SubscriptionResource
     * @throws AuthorizationException
     */
    public function store(StoreSubscriptionRequest $request): SubscriptionResource
    {
        $this->authorize("create", Subscription::class);

        $subscriptionRes = DB::transaction(function () use ($request) {
            $form               = $request->validated();
            $plan               = Plan::findOrFail($form["plan_id"]);
            $form["owner_id"]   = auth()->id();
            $form["owner_type"] = (new User())->getMorphClass();
            $user               = User::findOrFail(auth()->id());

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

            $trialStatus = SubscriptionModule::make()
                ->userCanCreateTrialPlan($form["owner_id"], $plan->id)
            ;

            abort_unless(
                $trialStatus["can_progress"],
                422,
                "You already trialed this plan."
            );

            abort_if(
                !(bool)$user->getMeta("is_shopify_user", null, "boolean") && $plan->gateway->slug == PaymentGatewaySlugEnum::SHOPIFY(),
                422,
                "Only shopify user can use this plan"
            );

            abort_if(
                (bool)$user->getMeta("is_shopify_user", null, "boolean") && $plan->gateway->slug != PaymentGatewaySlugEnum::SHOPIFY(),
                422,
                "Shopify plan must be provided for shopify user"
            );

            $form["cancel_at_period_end"] = (bool)$trialStatus["is_trial_plan"];

            $form["gateway_id"] = $plan->gateway_id;
            $form["gateway"]    = PaymentGateway::findOrFail($plan->gateway_id);
            $form["plan"]       = $plan;
            $form["quantity"]   = $form["plan"]->default_quantity;

            $request = new Request([
                "ownerable_id"   => auth()->id(),
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
                        "ownerable_id"   => auth()->id(),
                        "ownerable_type" => (new User())->getMorphClass(),
                        "email"          => auth()->user()->email,
                        "name"           => auth()->user()->name,
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

            if ($trialStatus["is_trial_plan"] && $trialStatus["plan_group_id"]) {
                UserTrialPlanGroup::create([
                    "user_id"       => $form["owner_id"],
                    "plan_group_id" => $trialStatus["plan_group_id"],
                    "plan_id"       => $plan->id
                ]);
            }

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
     * Update Subscription
     * @throws AuthorizationException
     * @throws Exception
     */
    public function update(UpdateSubscriptionRequest $request, Subscription $subscription): JsonResponse
    {
        if ($request->action == "cancel") {
            $this->authorize("cancel", Subscription::class);
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


    /**
     * Check Current User Subscription
     * @return JsonResponse|SubscriptionResource
     */
    public function userCurrentSubscription(): JsonResponse|SubscriptionResource
    {
        $user = User::find(1);
        $user->withMeta();

        $subModule              = SubscriptionModule::make();
        $activeSubscription     = $subModule->userCurrentSubscription($user);
        $incompleteSubscription = $subModule->incompleteSubscription($user);
        $allowances             = $subModule->getUserCurrentSubscribedAllAllowanceData($user);
//        $roles                  = $subModule->getUserCurrentSubscribedRoles($user);
        if (empty($activeSubscription) && empty($incompleteSubscription)) {
            return response()->json(
                [
                    "success" => false,
                    "message" => "User not subscribed to any plan",
                    "data"    => [
                        "validity_status" => UserSubscriptionStatusEnum::NOT_SUBSCRIBED(),
                    ]
                ],
                404
            );
        }

        if (!empty($incompleteSubscription)) {
            return response()->json(
                [
                    "success" => false,
                    "message" => "Subscription needs to be paid",
                    "data"    => [
                        "validity_status" => UserSubscriptionStatusEnum::HAVE_INCOMPLETE(),
                        "resume"          => [
                            "token" => "test" // TODO: Need to add payment token if have
                        ]
                    ]
                ]
            );
        }
        $activeSubscription->load("plan", "gateway");
        $planGroupPlan = PlanGroupPlan::query()
            ->where("plan_id", $activeSubscription->plan->id)
            ->first()
        ;

        $activeSubscription->plan->plan_group_id = $planGroupPlan?->plan_group_id;
        $cycleStart                              = $activeSubscription->getMeta(
            SubscriptionMeta::CURRENT_CYCLE_START(),
            null
        );
        $cycleEnd                                = $activeSubscription->getMeta(
            SubscriptionMeta::CURRENT_CYCLE_END(),
            null
        );

        return SubscriptionResource::new(
            $activeSubscription,
            [
                'allowances' => AllowanceResource::collection($allowances),
                'roles'      => RoleResource::collection($roles),
                'usage'      => [
                    "total_product_import" => $user->getMeta("total_product_import", 0),
                    "total_review_import"  => $user->getMeta("total_review_import", 0),
                ],
                'period'     => [
                    "start" => $cycleStart ? Carbon::createFromTimestamp($cycleStart)->toFormattedDateString() : null,
                    "end"   => $cycleEnd ? Carbon::createFromTimestamp($cycleEnd)->toFormattedDateString() : null
                ]
            ],
            UserSubscriptionStatusEnum::ACTIVE_SUBSCRIPTION()
        )->additional(
            [
                'success' => true,
                'message' => 'User subscription fetched successfully',
            ]
        );
    }

}
