<?php
/*
 * This file is part of the "taba secure 2-FACTOR AUTHNTICATION" plugin
 *
 * Copyright (C) SPREAD WORKS Inc. All Rights Reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plugin\Taba2FA\Util;

use Eccube\Common\Constant;

class Version
{
    public static function isSupportGetInstanceFunction()
    {
        return version_compare(Constant::VERSION, '3.0.9', '>=');
    }

    public static function isSupportLogFunction()
    {
        return version_compare(Constant::VERSION, '3.0.12', '>=');
    }

    public static function isSupportNewHookPoint()
    {
        return version_compare(Constant::VERSION, '3.0.9', '>=');
    }
}
