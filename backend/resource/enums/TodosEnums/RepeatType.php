<?php

namespace resource\enums\TodosEnums;

/**
 * 重复类型
 */
enum RepeatType: int
{
    case None = 0;
    case Daily = 1;
    case Weekly = 2;
    case Monthly = 3;
    case Yearly = 4;

    public function label(): string
    {
        return match ($this) {
            static::None => trans('None'),
            static::Daily => trans('Daily'),
            static::Weekly => trans('Weekly'),
            static::Monthly => trans('Monthly'),
            static::Yearly => trans('Yearly')
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
