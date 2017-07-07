<?php

namespace Spiral\Transactions\Database\Types;

use Spiral\ORM\Columns\EnumColumn;

class TransactionStatus extends EnumColumn
{
    const PURCHASED          = 'purchased';
    const PARTIALLY_REFUNDED = 'partially-refunded';
    const REFUNDED           = 'refunded';

    const VALUES  = [self::PURCHASED, self::PARTIALLY_REFUNDED, self::REFUNDED];
    const DEFAULT = self::PURCHASED;
}