<?php

namespace Spiral\Transactions\Database\Sources;

use Spiral\ORM\Entities\RecordSource;
use Spiral\Transactions\Database\Transaction\Refund;

class RefundSource extends RecordSource
{
    const RECORD = Refund::class;
}