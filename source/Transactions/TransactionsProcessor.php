<?php

namespace Spiral\Transactions;

use Spiral\Transactions\Database\Sources;
use Spiral\Transactions\Database\Transaction;
use Spiral\Transactions\Exceptions\EmptySourceIDGatewayException;
use Spiral\Transactions\Exceptions\GatewayException;
use Spiral\Transactions\Exceptions\Transaction\EmptyAmountException;
use Spiral\Transactions\Exceptions\Transaction\InvalidAmountException;
use Spiral\Transactions\Exceptions\Transaction\InvalidQuantityException;

class TransactionsProcessor
{
    /** @var Sources\ItemSource */
    protected $items;

    /** @var Sources\RevisionSource */
    protected $revisions;

    /** @var Transaction */
    protected $transaction;

    /** @var GatewayInterface */
    protected $gateway;

    /**
     * TransactionsProcessor constructor.
     *
     * @param Sources\TransactionSource $source
     * @param Sources\ItemSource        $itemSource
     * @param Sources\RevisionSource    $revisionSource
     * @param GatewayInterface          $gateway
     */
    public function __construct(
        Sources\TransactionSource $source,
        Sources\ItemSource $itemSource,
        Sources\RevisionSource $revisionSource,
        GatewayInterface $gateway
    ) {
        $this->items = $itemSource;
        $this->revisions = $revisionSource;
        $this->gateway = $gateway;
        $this->transaction = $source->create();
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
     */
    protected function add(string $title, float $amount, int $quantity, string $type): Transaction\Item
    {
        /** @var Transaction\Item $item */
        $item = $this->items->create();
        $item->title = $title;
        $item->amount = $amount;
        $item->quantity = $quantity;
        $item->type = $type;

        $this->transaction->items->push($item);
        $this->transaction->incTotalAmount($amount);

        return $item;
    }

    /**
     * @param PaymentSourceInterface $paymentSource
     * @param array                  $metadata
     *
     * @return Transaction
     * @throws EmptySourceIDGatewayException
     */
    public function makeTransaction(PaymentSourceInterface $paymentSource, array $metadata = []): Transaction
    {
        try {
            $transaction = $this->gateway->createTransaction($this->transaction, $paymentSource, $metadata);
            $this->transaction->revisions->add($this->makePurchaseRevision($transaction));
            $this->transaction->save();

            return $this->transaction;
        } catch (GatewayException $exception) {
            $this->transaction->setFailedStatus();
            //todo add attribute with error message
            $this->transaction->save();

            $this->packException();
        }

        //do some custom operations with transaction
        //cal gateway provider

        return $this->transaction;
    }

    /**
     * @param GatewayTransactionInterface $transaction
     *
     * @return Transaction\Revision
     */
    protected function makePurchaseRevision(GatewayTransactionInterface $transaction): Transaction\Revision
    {
        /** @var Transaction\Revision $revision */
        $revision = $this->revisions->create();
        $revision->setPurchase();
        $revision->setGatewayRawData($transaction->getRawData());
        $revision->setPaidAmount($transaction->getPaidAmount());
        $revision->setFeeAmount($transaction->getFeeAmount());
        $revision->setRefundedAmount($transaction->getRefundedAmount());

        return $revision;
    }

    /**
     * @param GatewayTransactionInterface $transaction
     */
    protected function fillPurchaseTransaction(GatewayTransactionInterface $transaction)
    {
        $this->transaction->setCompletedStatus();
        $this->transaction->setGatewayRawData($transaction->getRawData());
        $this->transaction->setGatewayTransactionID($transaction->getTransactionID());
        $this->transaction->setPaidAmount($transaction->getPaidAmount());
        $this->transaction->setFeeAmount($transaction->getFeeAmount());
        $this->transaction->setRefundedAmount($transaction->getRefundedAmount());
    }

    protected function packException()
    {

    }
}