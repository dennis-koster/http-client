<?php

namespace DennisKoster\HttpClient\Enums;

use MyCLabs\Enum\Enum;

/**
 * @method static CacheDurationsEnum DURATION_1_MIN()
 * @method static CacheDurationsEnum DURATION_5_MIN()
 * @method static CacheDurationsEnum DURATION_10_MIN()
 * @method static CacheDurationsEnum DURATION_15_MIN()
 * @method static CacheDurationsEnum DURATION_30_MIN()
 * @method static CacheDurationsEnum DURATION_1_HOUR()
 * @method static CacheDurationsEnum DURATION_1_DAY()
 * @method static CacheDurationsEnum DURATION_1_WEEK()
 */
class CacheDurationsEnum extends Enum
{
    public const DURATION_1_MIN = 60;
    public const DURATION_5_MIN = 5 * self::DURATION_1_MIN;
    public const DURATION_10_MIN = 10 * self::DURATION_1_MIN;
    public const DURATION_15_MIN = 15 * self::DURATION_1_MIN;
    public const DURATION_30_MIN = 30 * self::DURATION_1_MIN;
    public const DURATION_1_HOUR = 60 * self::DURATION_1_MIN;
    public const DURATION_1_DAY = 24 * self::DURATION_1_HOUR;
    public const DURATION_1_WEEK = 7 * self::DURATION_1_DAY;
}
