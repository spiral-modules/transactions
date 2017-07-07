<?php

namespace Spiral\Transactions\Database\Sources;

use Spiral\ORM\Entities\RecordSource;
use Spiral\Transactions\Database\Transaction\Source;

class SourceSource extends RecordSource
{
    const RECORD = Source::class;
}