<?php

namespace App\Enums;

use App\Base\Enum;

/**
 * Các trạng thái của user
 */
class UserStatus extends Enum
{
    const ACTIVE = 1;

    const DEACTIVE = 0;

    const ALL = null;
}
