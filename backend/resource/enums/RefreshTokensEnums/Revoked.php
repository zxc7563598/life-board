<?php

namespace resource\enums\RefreshTokensEnums;

/**
 * 是否销毁
 */
enum Revoked: int
{
    case No = 0;
    case Yes = 1;

    public function label(): string
    {
        return match ($this) {
            static::No => trans('no'),
            static::Yes => trans('yes')
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
