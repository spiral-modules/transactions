<?php

namespace Spiral\Transactions\Database\Sources;

use Spiral\ORM\Entities\RecordSource;
use Spiral\Transactions\Database\TransactionItem;

class TransactionItemSource extends RecordSource
{
    const RECORD = TransactionItem::class;
}