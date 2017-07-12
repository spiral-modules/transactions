<?php

namespace Spiral\Transactions\Gateways\Stripe;

use Spiral\Transactions\Exceptions\Gateway\ClientGatewayException;
use Spiral\Transactions\Exceptions\Gateway\InternalGatewayException;
use Spiral\Transactions\GatewayTransactionInterface;
use Spiral\Transactions\GatewayInterface;
use Spiral\Transactions\Sources\CreditCardSource;
use Spiral\Transactions\Sources\TokenSource;
use Spiral\Transactions\Configs\TransactionsConfig;
use Stripe\Charge;
use Stripe\Error;

/**
 * @link    https://stripe.com/docs/currencies
 * Class StripeGateway
 *
 * @package Spiral\Transactions\Gateways\Stripe
 */
class StripeGateway implements GatewayInterface
{
    const CONNECTION_EXCEPTION_MSG        = 'Network communication with Stripe failed, please retry.';
    const RATE_LIMIT_EXCEPTION_MSG        = 'Too many requests to Stripe, please retry later.';
    const REQUEST_EXCEPTION_MSG           = 'Invalid request, please contact webmaster.';
    const SETTINGS_EXCEPTION_MSG          = 'Stripe connection settings error, please contact webmaster.';
    const UNEXPECTED_STRIPE_EXCEPTION_MSG = 'Unexpected Stripe error occurred while processing%s, please contact webmaster.';
    const UNEXPECTED_EXCEPTION_MSG        = 'Unexpected error occurred while processing%s, please contact webmaster.';
    const UPD_EXCEPTION_MSG               = 'Error occurred while trying to retrieve Stripe charge';

    /** @var TransactionsConfig */
    protected $config;

    /** @var Fees */
    protected $fees;

    /** @var Refunds */
    protected $refunds;

    /**
     * StripeGateway constructor.
     *
     * @param TransactionsConfig $config
     * @param Fees               $fees
     * @param Refunds            $refunds
     */
    public function __construct(TransactionsConfig $config, Fees $fees, Refunds $refunds)
    {
        $this->config = $config;
        $this->fees = $fees;
        $this->refunds = $refunds;
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
     * Retrieve Stripe transaction.
     *
     * @param string $id
     *
     * @return GatewayTransactionInterface
     * @throws InternalGatewayException
     */
    public function updateTransaction(string $id): GatewayTransactionInterface
    {
        try {
            $charge = Charge::retrieve($id, $this->getOptions());
            $refunds = $this->refunds->getRefunds($charge);
            $fee = $this->fees->getFee($charge, $refunds);

            return new Entities\Transaction($charge, $fee, $refunds);
        } catch (\Throwable $exception) {
            throw new InternalGatewayException(self::UPD_EXCEPTION_MSG, $exception->getCode(), $exception);
        }
    }

    /**
     * Create Stripe transaction. Catch several errors for clients to be more informative.
     *
     * @param array $params
     *
     * @return Entities\Transaction
     * @throws InternalGatewayException|ClientGatewayException
     */
    protected function createTransaction(array $params): Entities\Transaction
    {
        try {
            $charge = Charge::create($params, $this->getOptions());
            $fee = $this->fees->getFee($charge);

            return new Entities\Transaction($charge, $fee);
        } catch (Error\Api $exception) {
            throw new ClientGatewayException(self::CONNECTION_EXCEPTION_MSG, $exception->getCode(), $exception);
        } catch (Error\ApiConnection $exception) {
            throw new ClientGatewayException(self::CONNECTION_EXCEPTION_MSG, $exception->getCode(), $exception);
        } catch (Error\Authentication $exception) {
            throw new InternalGatewayException(self::SETTINGS_EXCEPTION_MSG, $exception->getCode(), $exception);
        } catch (Error\Card $exception) {
            throw new ClientGatewayException($exception->getMessage(), $exception->getCode(), $exception);
        } catch (Error\RateLimit $exception) {
            throw new ClientGatewayException(self::RATE_LIMIT_EXCEPTION_MSG, $exception->getCode(), $exception);
        } catch (Error\InvalidRequest $exception) {
            throw new InternalGatewayException(self::REQUEST_EXCEPTION_MSG, $exception->getCode(), $exception);
        } catch (Error\Base $exception) {
            $code = $exception->getCode();
            $msg = sprintf(
                self::UNEXPECTED_STRIPE_EXCEPTION_MSG,
                !empty($code) ? sprintf(' (code: %s)', $code) : ''
            );
            throw new InternalGatewayException($msg, $exception->getCode(), $exception);
        } catch (\Throwable $exception) {
            $code = $exception->getCode();
            $msg = sprintf(
                self::UNEXPECTED_EXCEPTION_MSG,
                !empty($code) ? sprintf(' (code: %s)', $code) : ''
            );
            throw new InternalGatewayException($msg, $exception->getCode(), $exception);
        }
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
     * @throws InternalGatewayException
     */
    protected function paymentTokenMetadata(TokenSource $source): array
    {
        if (!empty($source->getCustomerID())) {
            if (empty($source->getSourceID())) {
                throw new InternalGatewayException('Unable to process transaction, no payment method specified.');
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

    /**
     * {@inheritdoc}
     */
    public function transactionGatewayURI(string $id): string
    {
        return sprintf(
            'https://dashboard.stripe.com/%s/payments/%s',
            $this->config->gatewayOption(self::class, 'environment'),
            $id
        );
    }
}