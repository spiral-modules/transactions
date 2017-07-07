<?php

namespace Spiral\Transactions\Database\Sources;

use Spiral\ORM\Entities\RecordSource;
use Spiral\Transactions\Database\Item;

class TransactionItemSource extends RecordSource
{
    const RECORD = Item::class;
}