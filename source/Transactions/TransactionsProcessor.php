<?php

namespace Spiral\Transactions;

use Spiral\Transactions\Database\Sources\TransactionItemSource;
use Spiral\Transactions\Database\Sources\TransactionSource;
use Spiral\Transactions\Database\Transaction;
use Spiral\Transactions\Database\TransactionItem;
use Spiral\Transactions\Exceptions\EmptyAmountArgumentException;
use Spiral\Transactions\Exceptions\EmptySourceIDGatewayException;
use Spiral\Transactions\Exceptions\InvalidAmountArgumentException;
use Spiral\Transactions\Exceptions\InvalidQuantityArgumentException;

class TransactionsProcessor
{
    /** @var TransactionItemSource */
    protected $items;

    /** @var Transaction */
    protected $transaction;

    /**
     * TransactionsProcessor constructor.
     *
     * @param TransactionSource     $source
     * @param TransactionItemSource $itemSource
     */
    public function __construct(TransactionSource $source, TransactionItemSource $itemSource)
    {
        $this->items = $itemSource;
        $this->transaction = $source->create();
    }

    /**
     * @param string $title    Purchased item title, max 255 chars.
     * @param float  $amount   Purchased item amount, should be positive.
     * @param int    $quantity Purchased item quantity, should be positive.
     *
     * @return TransactionItem
     * @throws InvalidAmountArgumentException
     * @throws InvalidQuantityArgumentException
     */
    public function addItem(string $title, float $amount, int $quantity): TransactionItem
    {
        if ($amount <= 0) {
            throw new InvalidAmountArgumentException($amount);
        }

        if ($quantity <= 0) {
            throw new InvalidQuantityArgumentException($quantity);
        }

        return $this->add($title, $amount, $quantity, TransactionItem::DEFAULT_TYPE);
    }

    /**
     * @param string $title  Regulation item title, max 255 chars.
     * @param float  $amount Regulation item amount, any sigh, not null (zero).
     *
     * @return TransactionItem
     * @throws EmptyAmountArgumentException
     */
    public function addRegulation(string $title, float $amount): TransactionItem
    {
        if (empty($amount)) {
            throw new EmptyAmountArgumentException();
        }

        return $this->add($title, $amount, 1, TransactionItem::REGULATION_TYPE);
    }

    /**
     * @param string $title  Discount item title, max 255 chars.
     * @param float  $amount Discount item amount, any sign, not null (zero).
     *
     * @return TransactionItem
     * @throws EmptyAmountArgumentException
     */
    public function addDiscount(string $title, float $amount): TransactionItem
    {
        if (empty($amount)) {
            throw new EmptyAmountArgumentException();
        }

        return $this->add($title, -abs($amount), 1, TransactionItem::DISCOUNT_TYPE);
    }

    /**
     * @param string $title
     * @param float  $amount
     * @param int    $quantity
     * @param string $type
     *
     * @return TransactionItem
     */
    protected function add(string $title, float $amount, int $quantity, string $type): TransactionItem
    {
        /** @var TransactionItem $item */
        $item = $this->items->create();
        $item->title = $title;
        $item->amount = $amount;
        $item->quantity = $quantity;
        $item->type = $type;

        $this->transaction->items->push($item);

        return $item;
    }

    /**
     * @param PaymentSourceInterface $paymentSource
     *
     * @return Transaction
     * @throws EmptySourceIDGatewayException
     */
    public function makeTransaction(PaymentSourceInterface $paymentSource): Transaction
    {
        $sourceID = $paymentSource->getGatewaySourceID();
        if (!empty($sourceID)) {
            throw new EmptySourceIDGatewayException();
        }
        if ($paymentSource->getGatewayCustomerID()) {
        } else {
            $source = [
                $paymentSource->getCardNumber(),
                $paymentSource->getExpYear(),
                $paymentSource->getExpMonth(),
                $paymentSource->getSecurityCode()
            ];
            $sourceOptions[] = $paymentSource->getCardHolder();
        }
        //do some custom operations with transaction
        //cal gateway provider

        return $this->transaction;
    }
}