<?php

namespace Spiral\Transactions\Gateways\Stripe;

use Stripe\BalanceTransaction;
use Stripe\Charge;

class StripeFees
{
    /** @var Charge */
    protected $charge;

    /**
     * StripeFees constructor.
     *
     * @param \Stripe\Charge $charge
     */
    public function __construct(Charge $charge)
    {
        $this->charge = $charge;
    }

    /**
     * @return float
     */
    public function getFee(): float
    {
        $balance = BalanceTransaction::retrieve($this->charge->balance_transaction);
        $fee = $balance->fee;

        foreach ($this->charge->refunds as $refund) {
            $balance = BalanceTransaction::retrieve($refund->balance_transaction);
            $fee += $balance->fee;
        }

        return $fee;
    }
}