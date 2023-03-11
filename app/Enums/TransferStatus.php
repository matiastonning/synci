<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static PENDING()
 * @method static static ERROR()
 * @method static static WARNING()
 * @method static static SUCCESS()
 */
final class TransferStatus extends Enum
{
    const PENDING = 0;
    const SUCCESS = 1;
    const WARNING = 2;
    const ERROR = 3;
}
