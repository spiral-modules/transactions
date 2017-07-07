<?php

namespace Spiral\Transactions;

use Spiral\Transactions\Database\Transaction\Revision;

interface GatewayTransactionInterface
{
    public function getRevision(): Revision;

    public function getTransactionID(): string;

    public function getFeeAmount(): float;

    public function getPaidAmount(): float;

    public function getRefundedAmount(): float;

    public function getRawData(): array;
}