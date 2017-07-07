<?php

namespace Spiral\Transactions;

use Spiral\Transactions\Database\Transaction\Attribute;
use Spiral\Transactions\Exceptions\TransactionException;
use Spiral\Transactions\PaymentSources\CreditCardSource;

class Example

{
    public function create(TransactionsProcessor $processor)
    {
        $processor->addItem('my item', 12, 2);
        $processor->addDiscount('my discount', 3);

        try {
            $transaction = $processor->payWithCreditCard(
                new CreditCardSource(['some', 'data', 'about', 'payment', 'source']),
                [''],
                [Attribute::IP_ADDRESS => '127.0.0.1']
            );

            return [
                'status' => 200,
                'data'=>['fee'=>$transaction->getf]
            ];
        } catch (TransactionException $e) {
            return [
                'status' => 400,
                'error'  => $e->getMessage()
            ];
        }
    }
}