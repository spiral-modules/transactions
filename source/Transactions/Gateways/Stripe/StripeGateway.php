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
use Stripe\Stripe;

class StripeGateway implements GatewayInterface
{
    const CONNECTION_EXCEPTION_MSG        = 'Network communication with Stripe failed, please retry.';
    const RATE_LIMIT_EXCEPTION_MSG        = 'Too many requests to Stripe, please retry later.';
    const REQUEST_EXCEPTION_MSG           = 'Invalid request, please contact webmaster.';
    const SETTINGS_EXCEPTION_MSG          = 'Stripe connection settings error, please contact webmaster.';
    const UNEXPECTED_STRIPE_EXCEPTION_MSG = 'Unexpected Stripe error occurred while processing%s, please contact webmaster.';
    const UNEXPECTED_EXCEPTION_MSG        = 'Unexpected error occurred while processing%s, please contact webmaster.';

    /** @var TransactionsConfig */
    protected $config;

    /**
     * StripeGateway constructor.
     *
     * @param TransactionsConfig $config
     */
    public function __construct(TransactionsConfig $config)
    {
        $this->config = $config;

        Stripe::setApiKey($this->getOptions()['api_key']);
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return $this->config->gatewayName(static::class);
    }

    /**
     * @return array
     */
    protected function getOptions(): array
    {
        return $this->config->gatewayOptions(static::class);
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
            $charge = Charge::create($params, $this->getOptions());

            return new StripeTransaction($charge);
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
            if (empty($source->getSourceID())) {
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