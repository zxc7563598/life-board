<?php

namespace resource\enums\CalendarHolidaysEnums;

/**
 * 是否是法定假期
 */
enum IsOfficialHoliday: int
{
    case No = 0;
    case Yes = 1;

    public function label(): string
    {
        return match ($this) {
            static::No => trans('no'),
            static::Yes => trans('yes'),
        };
    }

    public static function all(): array
    {
        $cases = self::cases();
        $enums = [];
        foreach ($cases as $_cases) {
            $enums[] = [
                'key' => $_cases->value,
                'value' => $_cases->label()
            ];
        }
        return $enums;
    }
}
