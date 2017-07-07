<?php

namespace Spiral\Transactions\Database\Sources;

use Spiral\ORM\Entities\RecordSource;
use Spiral\Transactions\Database\Transaction\Attribute;

class AttributeSource extends RecordSource
{
    const RECORD = Attribute::class;
}