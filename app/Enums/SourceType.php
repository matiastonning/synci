<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static Curve()
 */
final class SourceType extends Enum
{
    const Curve = 0;
    const Tink = 1;
    const Aiia = 2;
}
