<?php

namespace resource\enums\BillRecordsEnums;

/**
 * 收支类型
 */
enum IncomeType: int
{
    case Uncategorized = 0;
    case Income = 1;
    case Expense = 2;

    //定义一个转换函数，用来显示
    public function label(): string
    {
        return match ($this) {
            static::Uncategorized => trans('Uncategorized'),
            static::Income => trans('Income'),
            static::Expense => trans('Expense')
        };
    }

    // 获取全部的枚举
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