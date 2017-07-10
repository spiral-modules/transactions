<?php

namespace Spiral\Transactions\Bootloaders;

use Spiral\Core\Bootloaders\Bootloader;
use Spiral\Transactions\GatewayInterface;
use Spiral\Transactions\Gateways\Stripe\StripeGateway;

class TransactionsBootloader extends Bootloader
{
    const BINDINGS = [
        GatewayInterface::class => StripeGateway::class
    ];
}