<?php

namespace resource\enums\BillRecordsEnums;

/**
 * 平台
 */
enum Platform: int
{
    case Wechat = 0;
    case Alipay = 1;

    public function label(): string
    {
        return match ($this) {
            static::Wechat => trans('Wechat'),
            static::Alipay => trans('Alipay')
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
