<?php

namespace App\Enums;

use MoveOn\Common\Traits\BaseEnum;

/**
 * @method static PAYPAL()
 * @method static STRIPE()
 * @method static SHOPIFY()
 */
enum PaymentGatewaySlugEnum: string
{
    use BaseEnum;

    case PAYPAL = "paypal";
    case STRIPE = "stripe";
    case SHOPIFY = "shopify";
}
