<?php

namespace Spiral\Transactions\Database\Transaction;

use Spiral\ORM\Record;

class Source extends Record
{
    const SCHEMA = [
        'id'            => 'primary',
        'gateway_id'    => 'string, nullable',
        'currency'      => 'string(8)',
        'card_type'     => 'string',
        'card_holder'   => 'string',
        'exp_year'      => 'int',
        'exp_month'     => 'int',
        'number_ending' => 'string',
    ];

    const INDEXES = [
        [self::INDEX, 'currency'],
        [self::UNIQUE, 'gateway_source_id'],
    ];

    /**
     * {@inheritdoc}
     */
    public function getGatewayID()
    {
        return $this->gateway_id;
    }

    /**
     * {@inheritdoc}
     */
    public function getCardHolder(): string
    {
        return $this->card_holder;
    }

    /**
     * Card number.
     *
     * @return string
     */
    public function getCardNumber(): string
    {
        return '';
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
     * Card expiration month (12).
     *
     * @return int
     */
    public function getExpMonth(): int
    {
        return $this->exp_month;
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
     * Card number last digits (1234).
     *
     * @return string
     */
    public function getNumberEnding(): string
    {
        return $this->number_ending;
    }
}