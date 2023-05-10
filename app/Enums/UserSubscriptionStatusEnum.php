<?php

namespace App\Enums;

use MoveOn\Common\Traits\BaseEnum;

/**
 * @method static NOT_SUBSCRIBED()
 * @method static HAVE_INCOMPLETE()
 * @method static ACTIVE_SUBSCRIPTION()
*/
enum UserSubscriptionStatusEnum: string
{
    use BaseEnum;

    case NOT_SUBSCRIBED = 'not-subscribed';
    case HAVE_INCOMPLETE = 'incomplete-payment';
    case ACTIVE_SUBSCRIPTION = 'active-subscription';
}
