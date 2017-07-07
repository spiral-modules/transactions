<?php

namespace Spiral\Transactions;

class Example

{
    public function create(TransactionsProcessor $processor)
    {
        $processor->addItem('my item', 12, 2);
        $processor->addDiscount('my discount', 3);

        $transaction = $processor->makeTransaction(new TestPaymentSource(['some', 'data', 'about', 'payment', 'source']));
    }
}