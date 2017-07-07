<?php

namespace Spiral\Transactions\Database\Sources;

use Spiral\ORM\Entities\RecordSource;
use Spiral\Transactions\Database\Transaction\Item;

class ItemSource extends RecordSource
{
    const RECORD = Item::class;
}