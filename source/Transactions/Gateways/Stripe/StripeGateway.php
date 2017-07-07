<?php

namespace Spiral\Transactions\Gateways\Stripe;

use Spiral\Transactions\Database\Transaction;
use Spiral\Transactions\Exceptions\Gateway\EmptySourceException;
use Spiral\Transactions\Exceptions\GatewayException;
use Spiral\Transactions\GatewayTransactionInterface;
use Spiral\Transactions\GatewayInterface;
use Spiral\Transactions\PaymentSources\CreditCardSource;
use Spiral\Transactions\PaymentSources\TokenSource;
use Spiral\Transactions\TransactionsConfig;
use Stripe\BalanceTransaction;
use Stripe\Charge;
use Stripe\Error;

class StripeGateway implements GatewayInterface
{
    const GATEWAY_NAME = 'stripe';

    const CONNECTION_EXCEPTION_MSG        = 'Network communication with Stripe failed, please retry.';
    const RATE_LIMIT_EXCEPTION_MSG        = 'Too many requests to Stripe, please retry later.';
    const REQUEST_EXCEPTION_MSG           = 'Invalid request, please contact webmaster.';
    const SETTINGS_EXCEPTION_MSG          = 'Stripe connection settings error, please contact webmaster.';
    const UNEXPECTED_STRIPE_EXCEPTION_MSG = 'Unexpected Stripe error occurred while processing%s, please contact webmaster.';
    const UNEXPECTED_EXCEPTION_MSG        = 'Unexpected error occurred while processing%s, please contact webmaster.';

    /** @var array */
    protected $gatewayOptions = [];

    /**
     * StripeGateway constructor.
     *
     * @param TransactionsConfig $config
     */
    public function __construct(TransactionsConfig $config)
    {
        $this->gatewayOptions = $config->gatewayOptions(static::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return self::GATEWAY_NAME;
    }

    /**
     * @param float       $amount
     * @param string      $currency
     * @param TokenSource $source
     * @param array       $params
     *
     * @return GatewayTransactionInterface
     * @throws GatewayException
     */
    public function payWithToken(
        float $amount,
        string $currency,
        TokenSource $source,
        array $params = []
    ): GatewayTransactionInterface {
        $params += $this->paymentTransactionOptions($amount, $currency);
        $params += $this->paymentTokenMetadata($source);

        return $this->createTransaction($params);
    }

    /**
     * @param float            $amount
     * @param string           $currency
     * @param CreditCardSource $source
     * @param array            $params
     *
     * @return GatewayTransactionInterface
     * @throws GatewayException
     */
    public function payWithCreditCard(
        float $amount,
        string $currency,
        CreditCardSource $source,
        array $params = []
    ): GatewayTransactionInterface {
        $params += $this->paymentTransactionOptions($amount, $currency);
        $params += $this->paymentCreditCardMetadata($source);

        return $this->createTransaction($params);
    }

    /**
     * @param array $params
     *
     * @return StripeTransaction
     * @throws GatewayException
     */
    protected function createTransaction(array $params): StripeTransaction
    {
        try {
            $charge = Charge::create($params, $this->gatewayOptions);

            return new StripeTransaction($charge, $this->calculateFee($charge));
        } catch (Error\Api $exception) {
            $msg = self::CONNECTION_EXCEPTION_MSG;
        } catch (Error\ApiConnection $exception) {
            $msg = self::CONNECTION_EXCEPTION_MSG;
        } catch (Error\Authentication $exception) {
            $msg = self::SETTINGS_EXCEPTION_MSG;
        } catch (Error\Card $exception) {
            $msg = $exception->getMessage();
        } catch (Error\RateLimit $exception) {
            $msg = self::RATE_LIMIT_EXCEPTION_MSG;
        } catch (Error\InvalidRequest $exception) {
            $msg = self::REQUEST_EXCEPTION_MSG;
        } catch (Error\Base $exception) {
            $code = $exception->getCode();
            $msg = sprintf(
                self::UNEXPECTED_STRIPE_EXCEPTION_MSG,
                !empty($code) ? sprintf(' (code: %s)', $code) : ''
            );
        } catch (\Exception $exception) {
            $code = $exception->getCode();
            $msg = sprintf(
                self::UNEXPECTED_EXCEPTION_MSG,
                !empty($code) ? sprintf(' (code: %s)', $code) : ''
            );
        }

        throw new GatewayException($msg, $exception->getCode(), $exception);
    }

    /**
     * @param Charge $charge
     *
     * @return float
     */
    protected function calculateFee(Charge $charge): float
    {
        $balance = BalanceTransaction::retrieve($charge->balance_transaction);
        $fee = $balance->fee;

        foreach ($charge->refunds as $refund) {
            $balance = BalanceTransaction::retrieve($refund->balance_transaction);
            $fee += $balance->fee;
        }

        return $fee;
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
     * @param float  $amount
     * @param string $currency
     *
     * @return array
     */
    protected function paymentTransactionOptions(float $amount, string $currency): array
    {
        return [
            'amount'   => floor($amount),
            'currency' => strtolower($currency)
        ];
    }

    /**
     * @param TokenSource $source
     *
     * @return array
     * @throws EmptySourceException
     */
    protected function paymentTokenMetadata(TokenSource $source): array
    {
        if (!empty($source->getCustomerID())) {
            if (empty($source)) {
                throw new EmptySourceException();
            }

            return [
                'customer' => $source->getCustomerID(),
                'source'   => $source->getSourceID()
            ];
        } else {
            return [
                'source' => $source->getSourceID()
            ];
        }
    }

    /**
     * @param CreditCardSource $source
     *
     * @return array
     */
    protected function paymentCreditCardMetadata(CreditCardSource $source): array
    {
        return [
            'source' => [
                'object'    => 'card',
                'number'    => $source->getCardNumber(),
                'exp_month' => $source->getExpMonth(),
                'exp_year'  => $source->getExpYear(),
                'name'      => $source->getCardHolder(),
                'cvc'       => $source->getSecurityCode()
            ]
        ];
    }
}