<?php

namespace App\Enums;

use MoveOn\Common\Traits\BaseEnum;

/**
 * @method static WOOCOMMERCE()
 * @method static SHOPIFY()
 */
enum ShopSlugEnum: string
{
    use BaseEnum;

    case WOOCOMMERCE = 'woocommerce';
    case SHOPIFY = 'shopify';
}

