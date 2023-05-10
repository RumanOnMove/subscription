<?php

namespace App\Enums;

use MoveOn\Common\Traits\BaseEnum;

/**
 * @method static PENDING()
 * @method static ACTIVE()
 * @method static COMPLETED()
 */
enum SubscriptionSessionStatus: string
{
    use BaseEnum;

    case PENDING = 'pending';
    case ACTIVE = 'active';
    case COMPLETED = 'completed';
}
