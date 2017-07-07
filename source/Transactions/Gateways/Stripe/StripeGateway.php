<?php

namespace Spiral\Transactions\Gateways\Stripe;

use Spiral\Transactions\Database\Transaction;
use Spiral\Transactions\Exceptions\Gateway\EmptySourceException;
use Spiral\Transactions\Exceptions\GatewayException;
use Spiral\Transactions\GatewayTransactionInterface;
use Spiral\Transactions\GatewayInterface;
use Spiral\Transactions\PaymentSourceInterface;
use Spiral\Transactions\TransactionsConfig;
use Stripe\Charge;
use Stripe\Error\Base;

class StripeGateway implements GatewayInterface
{
    /**
     * Internal gateway name.
     */
    const GATEWAY = 'stripe';

    /** @var array */
    protected $gatewayOptions = [];

    public function __construct(TransactionsConfig $config)
    {
        $this->gatewayOptions = $config->getGatewayOptions(static::GATEWAY);
    }

    public function createTransaction(
        Transaction $transaction,
        PaymentSourceInterface $paymentSource,
        array $metadata = []
    ): GatewayTransactionInterface {
        $metadata += $this->prepareCreateChargeOptions($transaction, $paymentSource);

        try {
            return new StripeTransaction(Charge::create($metadata, $this->gatewayOptions));
        } catch (Base $stripeException) {
            //todo pack into different app exceptions
            throw new GatewayException(
                'Payment gateway exception occurred (code: ' . $stripeException->getCode() . ')',
                $stripeException->getCode(),
                $stripeException
            );
        } catch (\Exception $exception) {
            throw new GatewayException(
                'Payment gateway exception occurred (code: ' . $exception->getCode() . ')',
                $exception->getCode(),
                $exception
            );
        }
    }

    /**
     * Update transaction values.
     *
     * @param Transaction $transaction
     * @param Charge      $stripeCharge
     *
     * @return Transaction
     */
    protected function updateTransaction(Transaction $transaction, Charge $stripeCharge)
    {
        //Refund amount
        $transaction->amounts->refundedValue = $stripeCharge->amount_refunded / 100;

        //Fee value, todo: make sure this is OK
        $transaction->amounts->gatewayFee = self::feeFixed + self::feeRate * ($transaction->amounts->paidValue - $transaction->amounts->refundedValue);

        //Updating transaction
        $transaction->transactionID = $stripeCharge->id;
        $transaction->card->type = $stripeCharge->source->brand;

        if ($stripeCharge->refunds->data && is_array($stripeCharge->refunds->data)) {
            $lastID = $this->updateRefunds($transaction, $stripeCharge->refunds->data);

            if (count($stripeCharge->refunds->data) >= 10) {
                $refunds = $stripeCharge->refunds->all([
                    'limit'          => 100,
                    'starting_after' => $lastID
                ]);

                if ($refunds->data && is_array($refunds->data)) {
                    $this->updateRefunds($transaction, $refunds->data);
                }
            }
        }

        return $transaction;
    }

    /**
     * @param Transaction            $transaction
     * @param PaymentSourceInterface $paymentSource
     *
     * @return array
     * @throws EmptySourceException
     */
    protected function prepareCreateChargeOptions(Transaction $transaction, PaymentSourceInterface $paymentSource): array
    {
        $options = [
            'amount'   => round($transaction->getBillableAmount() * 100),
            'currency' => strtolower($transaction->getCurrency())
        ];

        $customer = $paymentSource->getCustomerID();
        $source = $paymentSource->getSourceID();

        if (!empty($customer)) {
            if (empty($source)) {
                throw new EmptySourceException();
            }

            $options['customer'] = $customer;
            $options['source'] = $source;
        } else {
            $options['source'] = $source ?: $this->fillSourceData($paymentSource);
        }

        return $options;
    }

    /**
     * @param PaymentSourceInterface $paymentSource
     *
     * @return array
     */
    protected function fillSourceData(PaymentSourceInterface $paymentSource): array
    {
        return [
            'number'    => $paymentSource->getCardNumber(),
            'exp_month' => $paymentSource->getExpMonth(),
            'exp_year'  => $paymentSource->getExpYear(),
            'name'      => $paymentSource->getCardHolder(),
            'cvc'       => $paymentSource->getSecurityCode()
        ];
    }
}