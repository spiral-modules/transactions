<?php

namespace Spiral\Transactions\Gateways\Stripe;

use Spiral\Transactions\TransactionsConfig;
use Spiral\Transactions\Gateways\Stripe\Entities;
use Stripe\Charge;
use Stripe\Refund;

class Refunds
{
    /** @var array */
    protected $config = [];
    const LIMIT = 1;

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
     * @param bool   $asArray
     *
     * @return array
     */
    public function getRefunds(Charge $charge, bool $asArray = false)
    {
        $refunds = [];
        if (!$charge->refunds->has_more) {
            foreach ($charge->refunds->data as $refund) {
                $refunds[$refund->id] = $this->makeRefundObject($refund, $asArray);
            }
        } else {
            $startingAfter = null;
            $calls = ceil($charge->refunds->total_count / self::LIMIT);
            for ($i = 0; $i < $calls; $i++) {
                $options = [
                    'charge' => $charge->id
                ];
                if (!empty($startingAfter)) {
                    $options['starting_after'] = $startingAfter;
                }

                $callRefunds = Refund::all($options, $this->options());

                foreach ($callRefunds->data as $id => $callRefund) {
                    if ($id === 0) {
                        $startingAfter = $callRefund->id;
                    }

                    if (!isset($refunds[$callRefund->id])) {
                        $refunds[$callRefund->id] = $this->makeRefundObject($callRefund, $asArray);
                    }
                }
            }
        }

        return $refunds;
    }

    /**
     * @param Refund $refund
     * @param bool   $asArray
     *
     * @return Entities\Refund|Refund
     */
    protected function makeRefundObject(Refund $refund, bool $asArray)
    {
        if (empty($asArray)) {
            return new Entities\Refund($refund);
        }

        return $refund;
    }

    /**
     * @return array
     */
    protected function options()
    {
        return $this->config->gatewayOptions(StripeGateway::class);
    }
}