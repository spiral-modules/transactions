<?php

namespace Spiral\Tests\Transactions;

use Spiral\Transactions\Database\Transaction;
use Spiral\Transactions\GatewayInterface;
use Spiral\Transactions\Gateways\Stripe\Fees;
use Spiral\Transactions\Gateways\Stripe\StripeGateway;
use Spiral\Transactions\Sources\CreditCardSource;
use Spiral\Transactions\Processors;
use Stripe\BalanceTransaction;
use Stripe\Charge;
use Stripe\Stripe;

class StriprTest extends \Spiral\Tests\BaseTest
{
    public function testCharge()
    {
        $this->container->bind(GatewayInterface::class, StripeGateway::class);
        /** @var Processors\PaymentsProcessor $processor */
        $processor = $this->container->get(Processors\PaymentsProcessor::class);

        $processor->addDiscount('td1', 1000);
        $processor->addDiscount('td2', -2000);
        $processor->addItem('i1', 1, 100);
        $processor->addItem('i2', 2, 200);
        $processor->addCorrection('c1', -3000);
        $processor->addCorrection('c2', 10000);

        $processor->payWithCreditCard(
            new CreditCardSource(
                '4242424242424242',
                12,
                2022,
                'vvval',
                '123'
            ),
            'gbp'
        );

        $t = $this->orm->source(Transaction::class)->findOne();
        $s = $this->orm->source(Transaction\Source::class)->findOne();
        print_r($t);
        print_r($s);
    }

    public function testTransaction()
    {
        /** @var Transaction $t */
        $t = $this->orm->source(Transaction::class)->create();
        $t->setGatewayID('ch_1AdwGg4vAcoZe0ezsXyl2cC8');
        $t->save();

        /** @var \Spiral\Transactions\Processors\UpdateProcessor $up */
        $this->container->bind(GatewayInterface::class, StripeGateway::class);
        $up = $this->container->make(Processors\UpdateProcessor::class);
        $up->update($t);

        /** @var Fees $fees */
//        $fees = $this->container->make(Fees::class);
//        /** @var Refunds $refunds */
//        $refunds = $this->container->make(Refunds::class);
//
//        Stripe::setApiKey(env('STRIPE_API_KEY'));
//        $charge = Charge::retrieve('ch_1AdwGg4vAcoZe0ezsXyl2cC8');
//        print_r("\n");
//        print_r($fees->getFee($charge));
//        print_r("\n");
//        print_r($refunds->getRefunds($charge));
//        print_r("\n");


        $t = $this->orm->source(Transaction::class)->findOne();
        print_r($t);

        foreach ($t->refunds as $refund) {
            print_r($refund);
        }
    }
}