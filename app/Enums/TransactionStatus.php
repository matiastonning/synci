<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static PENDING()
 * @method static static TRANSFERRED()
 * @method static static FETCHED()
 * @method static static FAILED_SOME()
 * @method static static FAILED_ALL()
 */
final class TransactionStatus extends Enum
{
    const PENDING = 0;
    const TRANSFERRED = 1;
    const TRANSFERRING = 2;
    const FAILED_SOME = 3;
    const FAILED_ALL = 4;
}
