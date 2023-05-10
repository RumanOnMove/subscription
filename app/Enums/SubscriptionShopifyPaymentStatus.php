<?php

namespace App\Enums;

use MoveOn\Common\Traits\BaseEnum;

/**
 * @method static UNDETERMINED()
 * @method static PAID()
 * @method static UNPAID()
 */
enum SubscriptionShopifyPaymentStatus: string
{
    use BaseEnum;

    case UNDETERMINED = "undetermined";
    case PAID = "paid";
    case UNPAID = "unpaid";
}

