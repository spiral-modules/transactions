<?php

use Spiral\Transactions\Gateways\Stripe\StripeGateway;

return [
    'gateways' => [
        StripeGateway::class => [
            'name'    => 'stripe',
            'options' => [
                //required
                'api_key'         => env('STRIPE_API_KEY'),
                //optional
                'idempotency_key' => env('STRIPE_IDEMPOTENCY_KEY'),
                'stripe_account'  => env('STRIPE_ACCOUNT'),
                'stripe_version'  => env('STRIPE_VERSION'),
            ]
        ]
    ]
];