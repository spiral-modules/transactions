<?php
/**
 * Created by PhpStorm.
 * User: Valentin
 * Date: 07.07.2017
 * Time: 12:03
 */

namespace Spiral\Transactions\Gateways\Stripe;


use Spiral\Transactions\Database\Transaction\Revision;
use Spiral\Transactions\GatewayTransactionInterface;
use Stripe\Charge;

class StripeTransaction implements GatewayTransactionInterface
{
    /** @var Charge */
    protected $charge;

    public function __construct(Charge $charge)
    {
        $this->charge = $charge;
    }

    public function getRawData(): array
    {
        // TODO: Implement getRawData() method.
    }

    public function getRevision(): Revision
    {
        // TODO: Implement getRevision() method.
    }

    public function getTransactionID(): string
    {
        // TODO: Implement getTransactionID() method.
    }
}