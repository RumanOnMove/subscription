<?php

namespace App\Enums;

use MoveOn\Common\Traits\BaseEnum;

/**
 * @method static SHOPIFY_SUBSCRIPTION_CANCELLED()
 * @method static SHOPIFY_SUBSCRIPTION_ACTIVATED()
 */
enum SubscriptionSessionDetailsType: string
{
    use BaseEnum;

    case SHOPIFY_SUBSCRIPTION_CANCELLED = 'shopify_subscription_cancelled';
    case SHOPIFY_SUBSCRIPTION_ACTIVATED = 'shopify_subscription_activated';
}
