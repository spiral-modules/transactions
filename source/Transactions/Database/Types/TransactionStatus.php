<?php

namespace Spiral\Transactions\Database\Types;

use Spiral\ORM\Columns\EnumColumn;

class TransactionStatus extends EnumColumn
{
    const PENDING            = 'pending';
    const COMPLETED          = 'completed';
    const FAILED             = 'failed';
    const PARTIALLY_REFUNDED = 'partially-refunded';
    const REFUNDED           = 'refunded';

    const VALUES  = [self::PENDING, self::COMPLETED, self::FAILED, self::PARTIALLY_REFUNDED, self::REFUNDED];
    const DEFAULT = self::PENDING;

    /**
     * Apply "completed" value.
     */
    public function setCompleted()
    {
        $this->setValue(self::COMPLETED);
    }

    /**
     * Apply "refunded" value.
     */
    public function setRefunded()
    {
        $this->setValue(self::REFUNDED);
    }

    /**
     * Apply "partially-refunded" value.
     */
    public function setPartiallyRefunded()
    {
        $this->setValue(self::PARTIALLY_REFUNDED);
    }

    /**
     * Apply "failed" value.
     */
    public function setFailed()
    {
        $this->setValue(self::FAILED);
    }
}