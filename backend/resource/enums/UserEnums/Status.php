<?php

namespace resource\enums\UserEnums;

/**
 * 是否启用
 */
enum Enable: int
{
    case Disable = 0;
    case Enable = 1;

    public function label(): string
    {
        return match ($this) {
            static::Disable => trans('disable'),
            static::Enable => trans('enable')
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
