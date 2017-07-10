<?php

namespace Spiral\Transactions\Database\Transaction;

use Spiral\ORM\Record;

/**
 * Class Source
 *
 * @property string $gateway_id
 * @package Spiral\Transactions\Database\Transaction
 */
class Source extends Record
{
    const SCHEMA = [
        'id'            => 'primary',
        'gateway_id'    => 'string, nullable',
        'card_type'     => 'string',
        'card_holder'   => 'string',
        'exp_month'     => 'int',
        'exp_year'      => 'int',
        'number_ending' => 'string',
    ];

    const INDEXES = [
        [self::UNIQUE, 'gateway_id'],
    ];

    /**
     * @return string
     */
    public function getGatewayID()
    {
        return $this->gateway_id;
    }

    /**
     * @param string $id
     */
    public function setGatewayID(string $id)
    {
        $this->gateway_id = $id;
    }

    /**
     * Card type (Maestro, Visa, AMEX, etc).
     *
     * @return string
     */
    public function getCardType(): string
    {
        return $this->card_type;
    }

    /**
     * @param string $cardType
     */
    public function setCardType(string $cardType)
    {
        $this->card_type = $cardType;
    }

    /**
     * @return string
     */
    public function getCardHolder(): string
    {
        return $this->card_holder;
    }

    /**
     * @param string $cardHolder
     */
    public function setCardHolder(string $cardHolder)
    {
        $this->card_holder = $cardHolder;
    }

    /**
     * Card expiration month (12).
     *
     * @return int
     */
    public function getExpMonth(): int
    {
        return $this->exp_month;
    }

    /**
     * @param int $month
     */
    public function setExpMonth(int $month)
    {
        $this->exp_month = $month;
    }

    /**
     * Card expiration year (2017).
     *
     * @return int
     */
    public function getExpYear(): int
    {
        return $this->exp_year;
    }

    /**
     * @param int $year
     */
    public function setExpYear(int $year)
    {
        $this->exp_year = $year;
    }

    /**
     * Card number last digits (1234).
     *
     * @return string
     */
    public function getNumberEnding(): string
    {
        return $this->number_ending;
    }

    /**
     * Card number last digits (1234).
     *
     * @param string $numberEnding
     */
    public function setNumberEnding(string $numberEnding)
    {
        $this->number_ending = $numberEnding;
    }
}