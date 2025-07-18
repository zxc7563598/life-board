<?php

namespace resource\enums\TodosEnums;

/**
 * 标签颜色
 */
enum Color: int
{
    case MintGreen = 1; // 薄荷绿
    case SkyBlue = 2; // 天空蓝
    case SteelBlue = 3; // 钢铁蓝
    case RosePink = 4; // 玫瑰粉
    case CaramelGold = 5; // 焦糖金
    case NeutralGray = 6; // 中性灰
    case AppleGreen = 7; // 苹果绿
    case CinnamonOrange = 8; // 肉桂橘
    case IndigoPurple = 9; // 靛蓝紫
    case CoralPink = 10; // 珊瑚粉
    case DeepTeal = 11; // 暗绿蓝
    case CoolBlueGray = 12; // 冷蓝灰
    case VintageMauve = 13; // 复古粉紫
    case GoldenBrown = 14; // 黄棕
    case DarkNeutralGray = 15; // 深中性灰
    case DeepGrassGreen = 16; // 深草绿
    case BrickRed = 17; // 砖红
    case DeepIndigo = 18; // 深蓝紫
    case DarkRosePink = 19; // 暗玫瑰粉
    case SoftPlumRed = 20; // 柔紫红

    public function label(): string
    {
        return match ($this) {
            static::MintGreen => trans('Mint Green'),
            static::SkyBlue => trans('Sky Blue'),
            static::SteelBlue => trans('Steel Blue'),
            static::RosePink => trans('Rose Pink'),
            static::CaramelGold => trans('Caramel Gold'),
            static::NeutralGray => trans('Neutral Gray'),
            static::AppleGreen => trans('Apple Green'),
            static::CinnamonOrange => trans('Cinnamon Orange'),
            static::IndigoPurple => trans('Indigo Purple'),
            static::CoralPink => trans('Coral Pink'),
            static::DeepTeal => trans('Deep Teal'),
            static::CoolBlueGray => trans('Cool Blue Gray'),
            static::VintageMauve => trans('Vintage Mauve'),
            static::GoldenBrown => trans('Golden Brown'),
            static::DarkNeutralGray => trans('Dark Neutral Gray'),
            static::DeepGrassGreen => trans('Deep Grass Green'),
            static::BrickRed => trans('Brick Red'),
            static::DeepIndigo => trans('Deep Indigo'),
            static::DarkRosePink => trans('Dark Rose Pink'),
            static::SoftPlumRed => trans('Soft Plum Red'),
        };
    }

    public function color(): array
    {
        return match ($this) {
            static::MintGreen => ['light' => '#55bfa7', 'dark' => '#3da890'], // 薄荷绿
            static::SkyBlue => ['light' => '#6998d0', 'dark' => '#4a76b0'], // 天空蓝
            static::SteelBlue => ['light' => '#5677a0', 'dark' => '#3c5b87'], // 钢铁蓝
            static::RosePink => ['light' => '#be8798', 'dark' => '#a06a7f'], // 玫瑰粉
            static::CaramelGold => ['light' => '#b78f48', 'dark' => '#9a7137'], // 焦糖金
            static::NeutralGray => ['light' => '#7f7f7f', 'dark' => '#666666'], // 中性灰
            static::AppleGreen => ['light' => '#8eb24e', 'dark' => '#6f963d'], // 苹果绿
            static::CinnamonOrange => ['light' => '#c68667', 'dark' => '#a2654e'], // 肉桂橘
            static::IndigoPurple => ['light' => '#687fc2', 'dark' => '#4e64a8'], // 靛蓝紫
            static::CoralPink => ['light' => '#e29b9b', 'dark' => '#bb7474'], // 珊瑚粉
            static::DeepTeal => ['light' => '#508e80', 'dark' => '#3b6e64'], // 暗绿蓝
            static::CoolBlueGray => ['light' => '#5f93b0', 'dark' => '#457691'], // 冷蓝灰
            static::VintageMauve => ['light' => '#ab7c95', 'dark' => '#8c627a'], // 复古粉紫
            static::GoldenBrown => ['light' => '#9e843e', 'dark' => '#7f6a2f'], // 黄棕
            static::DarkNeutralGray => ['light' => '#6b6b6b', 'dark' => '#555555'], // 深中性灰
            static::DeepGrassGreen => ['light' => '#7ca84a', 'dark' => '#618635'], // 深草绿
            static::BrickRed => ['light' => '#b77555', 'dark' => '#965b40'], // 砖红
            static::DeepIndigo => ['light' => '#4f5a95', 'dark' => '#3d4679'], // 深蓝紫
            static::DarkRosePink => ['light' => '#b0708a', 'dark' => '#90586f'], // 暗玫瑰粉
            static::SoftPlumRed => ['light' => '#a35b6d', 'dark' => '#854454'], // 柔紫红
        };
    }

    public static function all(): array
    {
        $cases = self::cases();
        $enums = [];
        foreach ($cases as $_cases) {
            $enums[] = [
                'key' => $_cases->value,
                'value' => $_cases->label(),
                'color' => $_cases->color()
            ];
        }
        return $enums;
    }
}
