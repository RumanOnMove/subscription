<?php

namespace App\Enums;

use MoveOn\Common\Traits\BaseEnum;

/**
 * @method static ACTIVE()
 * @method static INACTIVE()
*/
enum WebsiteStatusEnum: string
{
    use BaseEnum;

    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
}
