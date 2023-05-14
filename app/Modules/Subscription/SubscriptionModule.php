<?php /** @noinspection PhpIncompatibleReturnTypeInspection */

namespace App\Modules\Subscription;

use App\Models\Allowance;
use App\Models\PlanGroupPlan;
use App\Models\ShopAllowance;
use App\Models\User;
use App\Models\UserTrialPlanGroup;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use MoveOn\Common\Traits\Makeable;
use MoveOn\Subscription\Enums\SubscriptionStatus;
use MoveOn\Subscription\Models\Subscription;

class SubscriptionModule
{
    use Makeable;

    public function userCurrentSubscription(User $user): ?Subscription
    {
        return $user->subscriptions()->where(
            fn($q) => $q
                ->where('status', SubscriptionStatus::STRIPE_ACTIVE())
                ->orWhere('status', SubscriptionStatus::STRIPE_TRIALING())
                ->orWhere('status', SubscriptionStatus::PAYPAL_ACTIVE())
                ->orWhere('status', SubscriptionStatus::PAYPAL_APPROVED())
        )->first();
    }

    public function getUserCurrentSubscribedAllAllowanceDataOld($user): Collection|array
    {
        $shopAllowances = ShopAllowance::query()
            ->with("shop")
            ->withMeta('store_allowance')
            ->whereIn(
                'id',
                $user->userAbilities()->where('model_type', (new ShopAllowance())->getMorphClass())->select('model_id')
            )
            ->get();

        return [
            'shopAllowance' => $shopAllowances,
        ];
    }

    public function getUserCurrentSubscribedAllAllowanceData($user): Collection|array
    {
        return Allowance::query()
            ->with(
                [
                    "shop",
                    "storeRelatedAllowances",
                    "unlinkedAllowance",
                    "storeRelatedAllowances.linkedOf",
                    "unlinkedAllowance.linkedOf",
                ]
            )
            ->whereIn(
                'id',
                $user->userAbilities()->where('model_type', (new Allowance())->getMorphClass())->select('model_id')
            )
            ->get();
    }

//    public function getUserCurrentSubscribedRoles($user): Collection
//    {
//        return Role::query()
//            ->whereIn(
//                'id',
//                $user->userAbilities()->where('model_type', (new Role())->getMorphClass())->select('model_id')
//            )
//            ->get();
//    }

    /**
     * Return if user have active permission
     *
     * @param $user
     *
     * @return bool
     */
    public function userHaveActiveSubscription($user) : bool
    {
        return $user->subscriptions()->where(
            fn($q) => $q
                ->where('status', SubscriptionStatus::STRIPE_ACTIVE())
                ->orWhere('status', SubscriptionStatus::STRIPE_TRIALING())
                ->orWhere('status', SubscriptionStatus::PAYPAL_ACTIVE())
                ->orWhere('status', SubscriptionStatus::PAYPAL_APPROVED())
        )->count() > 0;
    }

    public function incompleteSubscription($user)
    {
        return $user->subscriptions()->where(
            fn($q) => $q
                ->orWhere('status', SubscriptionStatus::STRIPE_INCOMPLETE())
                ->orWhere('status', SubscriptionStatus::PAYPAL_APPROVAL_PENDING())
                ->orWhere('status', SubscriptionStatus::SHOPIFY_PENDING())
            )->where('updated_at', '>=', Carbon::now() ->subMinutes(2)->toDateTimeString())
            ->first();
    }

    public function userCanCreateNewSubscription($user)
    {
        $hasPotentialSubscription =         $user->subscriptions()->where(
            fn($q) => $q
                ->where('status', SubscriptionStatus::STRIPE_ACTIVE())
                ->orWhere('status', SubscriptionStatus::STRIPE_INCOMPLETE())
                ->orWhere('status', SubscriptionStatus::STRIPE_TRIALING())
                ->orWhere('status', SubscriptionStatus::PAYPAL_ACTIVE())
                ->orWhere('status', SubscriptionStatus::PAYPAL_APPROVED())
                ->orWhere('status', SubscriptionStatus::PAYPAL_APPROVAL_PENDING())
        )->select('id', 'status')->first();


        if ($hasPotentialSubscription) {
            // now
            // now - 10min = old
            // updated_at >= old == all subscription after (10 min from now)
            $incompleteSubscriptions = $user->subscriptions()->where(
                fn($q) => $q
                    ->orWhere('status', SubscriptionStatus::STRIPE_INCOMPLETE())
                    ->orWhere('status', SubscriptionStatus::PAYPAL_APPROVAL_PENDING())
            )->select('id', 'status')
             ->where('updated_at', '>=', Carbon::now()
                                               ->subMinutes(2)
                                               ->toDateTimeString())
             ->first();

            if (!empty($incompleteSubscriptions)){
                return false;
            }

            return true;
        }

        return true;
    }

    /**
     * Determine whether user can trial a plan
     *
     * @param  int  $userId
     * @param  int  $planId
     *
     * @return array
     */
    public function userCanCreateTrialPlan(int $userId, int $planId): array
    {
        $planGroupPlan = PlanGroupPlan::query()->where([
                        ["plan_id", $planId],
                        ["is_trial", true]
                    ])->first();

        if ($planGroupPlan) {
            $alreadyTrialed = UserTrialPlanGroup::query()->where([
                "user_id"       => $userId,
                "plan_group_id" => $planGroupPlan->plan_group_id
            ])->first();

            return [
                "is_trial_plan" => true,
                "can_progress"  => !$alreadyTrialed,
                "plan_group_id" => $planGroupPlan->plan_group_id
            ];
        }

        return [
            "is_trial_plan" => false,
            "can_progress"  => true,
            "plan_group_id" => null
        ];
    }

    public function removeActiveSubscriptionOfUser(User $user): void
    {
        $subscription = $this->userCurrentSubscription($user);

        $user->userAbilities()->delete();

        $subscription->delete();
    }
}
