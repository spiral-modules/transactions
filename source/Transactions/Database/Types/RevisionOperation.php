<?php

namespace Spiral\Transactions\Database\Types;

use Spiral\ORM\Columns\EnumColumn;

class RevisionOperation extends EnumColumn
{
    const PURCHASE = 'purchase';
    const REFUND   = 'refund';

    const VALUES  = [self::PURCHASE, self::REFUND];
    const DEFAULT = self::PURCHASE;

    /**
     * Apply "purchase" value.
     */
    public function setPurchase()
    {
        $this->setValue(self::PURCHASE);
    }
}