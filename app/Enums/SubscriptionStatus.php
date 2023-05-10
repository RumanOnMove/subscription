<?php

namespace App\Enums;

use MoveOn\Common\Traits\BaseEnum;

/**
 * @method static STRIPE_INCOMPLETE()
 * @method static STRIPE_INCOMPLETE_EXPIRED()
 * @method static STRIPE_TRIALING()
 * @method static STRIPE_ACTIVE()
 * @method static STRIPE_PAST_DUE()
 * @method static STRIPE_CANCELED()
 * @method static STRIPE_UNPAID()
 *
 * @method static PAYPAL_APPROVAL_PENDING()
 * @method static PAYPAL_APPROVED()
 * @method static PAYPAL_ACTIVE()
 * @method static PAYPAL_SUSPENDED()
 * @method static PAYPAL_CANCELED()
 * @method static PAYPAL_EXPIRED()
 * @method static PAYPAL_PAYMENT_FAILED()
 *
 * @method static SHOPIFY_PENDING()
 * @method static SHOPIFY_ACTIVE()
 * @method static SHOPIFY_DECLINED()
 * @method static SHOPIFY_EXPIRED()
 * @method static SHOPIFY_FROZEN()
 * @method static SHOPIFY_CANCELLED()
 *
 * @method static SHOPIFY_GRAPH_PENDING()
 * @method static SHOPIFY_GRAPH_ACTIVE()
 * @method static SHOPIFY_GRAPH_DECLINED()
 * @method static SHOPIFY_GRAPH_EXPIRED()
 * @method static SHOPIFY_GRAPH_FROZEN()
 * @method static SHOPIFY_GRAPH_CANCELLED()
 */
enum SubscriptionStatus: string
{
    use BaseEnum;

    case SHOPIFY_PENDING = "pending";
    case SHOPIFY_ACTIVE = "active";
    case SHOPIFY_DECLINED = "declined";
    case SHOPIFY_EXPIRED = "expired";
    case SHOPIFY_FROZEN = "frozen";
    case SHOPIFY_CANCELLED = "cancelled";

    case SHOPIFY_GRAPH_PENDING = "PENDING";
    case SHOPIFY_GRAPH_ACTIVE = "ACTIVE";
    case SHOPIFY_GRAPH_DECLINED = "DECLINED";
    case SHOPIFY_GRAPH_EXPIRED = "EXPIRED";
    case SHOPIFY_GRAPH_FROZEN = "FROZEN";
    case SHOPIFY_GRAPH_CANCELLED = "CANCELLED";

    protected static function merge(): array
    {
        return [
            \MoveOn\Subscription\Enums\SubscriptionStatus::class,
        ];
    }
}

