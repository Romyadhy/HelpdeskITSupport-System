<?php

namespace App\Helpers;

use Carbon\Carbon;

class DateHelper
{
    public static function nowWITA(): Carbon
    {
        return Carbon::now('Asia/Makassar');
    }

    public static function todayWita(): string
    {
        return self::nowWita()->toDateString();
    }

    //for database
    public static function todayWitaUtcRange(): array
    {
        return [
            self::nowWita()->startOfDay()->utc(),
            self::nowWita()->endOfDay()->utc(),
        ];
    }
}
