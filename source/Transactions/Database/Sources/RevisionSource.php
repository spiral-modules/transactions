<?php

namespace Spiral\Transactions\Database\Sources;

use Spiral\ORM\Entities\RecordSource;
use Spiral\Transactions\Database\Transaction\Revision;

class RevisionSource extends RecordSource
{
    const RECORD = Revision::class;
}