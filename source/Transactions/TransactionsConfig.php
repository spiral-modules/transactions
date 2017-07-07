<?php
/**
 * Created by PhpStorm.
 * User: Valentin
 * Date: 07.07.2017
 * Time: 11:40
 */

namespace Spiral\Transactions;


use Spiral\Core\InjectableConfig;
use Spiral\Transactions\Gateways\StripeGateway;

class TransactionsConfig extends InjectableConfig
{
    const CONFIG = 'modules/transactions';

    protected $config = [
        StripeGateway::GATEWAY => [
            //required
            'api_key'         => '',
            //optional
            'idempotency_key' => '',
            'stripe_account'  => '',
            'stripe_version'  => '',
        ]
    ];

    public function getGatewayOptions(string $gateway): array
    {

    }
}