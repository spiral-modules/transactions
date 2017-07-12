<?php

namespace Spiral\Transactions\Processors;

use Spiral\Transactions\Database\Sources;
use Spiral\Transactions\Database\Transaction;
use Spiral\Transactions\Exceptions\Transaction\EmptyAmountException;
use Spiral\Transactions\Exceptions\Transaction\InvalidAmountException;
use Spiral\Transactions\Exceptions\Transaction\InvalidQuantityException;
use Spiral\Transactions\Exceptions\AbstractTransactionException;
use Spiral\Transactions\GatewayInterface;
use Spiral\Transactions\GatewayTransactionInterface;
use Spiral\Transactions\Sources\CreditCardSource;
use Spiral\Transactions\Sources\TokenSource;

class PaymentsProcessor
{
    /** @var Sources\ItemSource */
    protected $items;

    /** @var Sources\AttributeSource */
    protected $attributes;

    protected $sources;

    /** @var Transaction */
    protected $transaction;

    /** @var GatewayInterface */
    protected $gateway;

    /**
     * TransactionsProcessor constructor.
     *
     * @param Sources\TransactionSource $source
     * @param Sources\ItemSource        $itemSource
     * @param Sources\AttributeSource   $attributeSource
     * @param Sources\SourceSource      $sourceSource
     * @param GatewayInterface          $gateway
     */
    public function __construct(
        Sources\TransactionSource $source,
        Sources\ItemSource $itemSource,
        Sources\AttributeSource $attributeSource,
        Sources\SourceSource $sourceSource,
        GatewayInterface $gateway
    ) {
        $this->transaction = $source->create();

        $this->items = $itemSource;
        $this->attributes = $attributeSource;
        $this->sources = $sourceSource;
        $this->gateway = $gateway;
    }

    /**
     * @param string $title    Purchased item title, max 255 chars.
     * @param float  $amount   Purchased item amount, should be positive.
     * @param int    $quantity Purchased item quantity, should be positive.
     *
     * @return Transaction\Item
     * @throws InvalidAmountException
     * @throws InvalidQuantityException
     */
    public function addItem(string $title, float $amount, int $quantity): Transaction\Item
    {
        if ($amount <= 0) {
            throw new InvalidAmountException($amount);
        }

        if ($quantity <= 0) {
            throw new InvalidQuantityException($quantity);
        }

        return $this->add($title, $amount, $quantity, Transaction\Item::DEFAULT_TYPE);
    }

    /**
     * @param string $title  Correction item title, max 255 chars.
     * @param float  $amount Correction item amount, any sigh, not null (zero).
     *
     * @return Transaction\Item
     * @throws EmptyAmountException
     */
    public function addCorrection(string $title, float $amount): Transaction\Item
    {
        if (empty($amount)) {
            throw new EmptyAmountException();
        }

        return $this->add($title, $amount, 1, Transaction\Item::CORRECTION_TYPE);
    }

    /**
     * @param string $title  Discount item title, max 255 chars.
     * @param float  $amount Discount item amount, any sign, not null (zero).
     *
     * @return Transaction\Item
     * @throws EmptyAmountException
     */
    public function addDiscount(string $title, float $amount): Transaction\Item
    {
        if (empty($amount)) {
            throw new EmptyAmountException();
        }

        return $this->add($title, -abs($amount), 1, Transaction\Item::DISCOUNT_TYPE);
    }

    /**
     * @param string $title
     * @param float  $amount
     * @param int    $quantity
     * @param string $type
     *
     * @return Transaction\Item
     * @throws AbstractTransactionException
     */
    protected function add(string $title, float $amount, int $quantity, string $type): Transaction\Item
    {
        /** @var Transaction\Item $item */
        $item = $this->items->create();
        $item->setType($type);
        $item->setAmount($amount);
        $item->setQuantity($quantity);
        $item->setTitle($title);

        $this->transaction->items->add($item);
        $this->transaction->incPaidAmount($amount * $quantity);

        return $item;
    }

    /**
     * @param TokenSource $source
     * @param string      $currency
     * @param array       $params
     * @param array       $attributes
     *
     * @return Transaction
     */
    public function payWithToken(
        TokenSource $source,
        string $currency = 'usd',
        array $params = [],
        array $attributes = []
    ): Transaction {
        $transaction = $this->gateway->payWithToken($this->transaction->getPaidAmount(), $currency, $source, $params);

        return $this->pay($transaction, $attributes);
    }

    /**
     * @param string           $currency
     * @param CreditCardSource $source
     * @param array            $params
     * @param array            $attributes
     *
     * @return Transaction
     * @throws AbstractTransactionException
     */
    public function payWithCreditCard(
        CreditCardSource $source,
        string $currency = 'usd',
        array $params = [],
        array $attributes = []
    ): Transaction {
        $transaction = $this->gateway->payWithCreditCard($this->transaction->getPaidAmount(), $currency, $source, $params);

        return $this->pay($transaction, $attributes);
    }

    /**
     * @param GatewayTransactionInterface $transaction
     * @param array                       $attributes
     *
     * @return Transaction
     */
    protected function pay(GatewayTransactionInterface $transaction, array $attributes)
    {
        $this->fillTransaction($transaction);
        $this->fillAttributes($attributes);
        $this->fillSource($transaction);
        $this->transaction->save();

        return $this->transaction;
    }

    /**
     * @param GatewayTransactionInterface $transaction
     */
    protected function fillTransaction(GatewayTransactionInterface $transaction)
    {
        $this->transaction->setCompletedStatus();
        $this->transaction->setGateway($this->gateway->getName());
        $this->transaction->setGatewayID($transaction->getTransactionID());
        $this->transaction->setCurrency($transaction->getCurrency());
        $this->transaction->setPaidAmount($transaction->getPaidAmount());
        $this->transaction->setFeeAmount($transaction->getFeeAmount());
    }

    /**
     * @param GatewayTransactionInterface $transaction
     */
    protected function fillSource(GatewayTransactionInterface $transaction)
    {
        /** @var Transaction\Source $source */
        $source = $this->sources->create();
        $transactionSource = $transaction->getSource();

        $source->setGatewayID($transactionSource->getSourceID());
        $source->setCardType($transactionSource->getType());
        $source->setCardHolder($transactionSource->getCardHolder());
        $source->setExpMonth($transactionSource->getExpMonth());
        $source->setExpYear($transactionSource->getExpYear());
        $source->setNumberEnding($transactionSource->getNumberEnding());

        $this->transaction->source = $source;
    }

    /**
     * @param array $attributes
     */
    protected function fillAttributes(array $attributes)
    {
        foreach ($attributes as $name => $value) {
            $this->transaction->attributes->add(
                $this->attributes->create(compact('name', 'value'))
            );
        }
    }
}