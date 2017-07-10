<?php

namespace Spiral\Transactions\Gateways\Stripe;

use Spiral\Transactions\Configs\TransactionsConfig;
use Stripe\Charge;
use Stripe\Refund;

class Refunds
{
    /** @var TransactionsConfig */
    protected $config = [];

    const LIMIT = 100;

    /**
     * Refunds constructor.
     *
     * @param TransactionsConfig $config
     */
    public function __construct(TransactionsConfig $config)
    {
        $this->config = $config;
    }

    /**
     * @param Charge $charge
     *
     * @return Refund[]
     */
    public function getRefunds(Charge $charge)
    {
        $refunds = [];
        if (!$charge->refunds->has_more) {
            foreach ($charge->refunds->data as $refund) {
                $refunds[$refund->id] = $refund;
            }
        } else {
            $startingAfter = null;
            $calls = ceil($charge->refunds->total_count / self::LIMIT);

            for ($i = 0; $i < $calls; $i++) {
                $options = $this->refundsQuery($charge, $startingAfter);
                $callRefunds = Refund::all($options, $this->options());

                foreach ($callRefunds->data as $id => $callRefund) {
                    if (!empty($startingAfter)) {
                        $startingAfter = $callRefund->id;
                    }

                    if (!isset($refunds[$callRefund->id])) {
                        $refunds[$callRefund->id] = $callRefund;
                    }
                }
            }
        }

        return $refunds;
    }

    /**
     * @param \Stripe\Charge $charge
     * @param string|null    $startingAfter
     *
     * @return array
     */
    protected function refundsQuery(Charge $charge, string $startingAfter = null): array
    {
        $options = [
            'limit'  => self::LIMIT,
            'charge' => $charge->id
        ];

        if (!empty($startingAfter)) {
            $options['starting_after'] = $startingAfter;
        }

        return $options;
    }

    /**
     * @return array
     */
    protected function options()
    {
        return $this->config->gatewayOptions(StripeGateway::class);
    }
}