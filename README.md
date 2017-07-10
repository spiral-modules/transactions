# transactions
Stripe transactions integration and Vault controls


### Pay
```php
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
    new CreditCardSource('4242424242424242', 12, 2022, 'name', '123'),
    'gbp'
);
```

### Update data (refunds, fees, status)
```php
/** @var \Spiral\Transactions\Processors\UpdateProcessor $processor */
$processor = $this->container->make(Processors\UpdateProcessor::class);
$processor->update($transaction);
```
