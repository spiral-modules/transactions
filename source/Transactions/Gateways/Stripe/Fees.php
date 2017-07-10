<?php

namespace Spiral\Transactions\Gateways\Stripe;

use Spiral\Transactions\Configs\TransactionsConfig;
use Stripe\BalanceTransaction;
use Stripe\Charge;

class Fees
{
    /** @var array */
    protected $config = [];

    /** @var Refunds */
    protected $refunds;

    /**
     * Fees constructor.
     *
     * @param TransactionsConfig $config
     * @param Refunds            $refunds
     */
    public function __construct(TransactionsConfig $config, Refunds $refunds)
    {
        $this->config = $config;
        $this->refunds = $refunds;
    }

    /**
     * @param Charge $charge
     *
     * @return float
     */
    public function getFee(Charge $charge): float
    {
        $balance = BalanceTransaction::retrieve($charge->balance_transaction, $this->options());
        $fee = $balance->fee;

        foreach ($this->refunds->getRefunds($charge, true) as $refund) {
            $balance = BalanceTransaction::retrieve($refund->balance_transaction, $this->options());
            $fee += $balance->fee;
        }

        return $fee;
    }

    /**
     * @return array
     */
    protected function options()
    {
        return $this->config->gatewayOptions(StripeGateway::class);
    }
}