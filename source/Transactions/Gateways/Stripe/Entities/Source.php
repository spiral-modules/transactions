<?php

namespace Spiral\Transactions\Gateways\Stripe\Entities;

use Spiral\Transactions\GatewaySourceInterface;

class Source implements GatewaySourceInterface
{
    /** @var array */
    protected $data;

    /**
     * Source constructor.
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * {@inheritdoc}
     */
    public function getSourceID(): string
    {
        return $this->data['id'];
    }

    /**
     * {@inheritdoc}
     */
    public function getType(): string
    {
        return $this->data['brand'];
    }

    /**
     * {@inheritdoc}
     */
    public function getCardHolder(): string
    {
        return $this->data['name'];
    }

    /**
     * {@inheritdoc}
     */
    public function getExpMonth(): string
    {
        return $this->data['exp_month'];
    }

    /**
     * {@inheritdoc}
     */
    public function getExpYear(): string
    {
        return $this->data['exp_year'];
    }

    /**
     * {@inheritdoc}
     */
    public function getNumberEnding(): string
    {
        return $this->data['last4'];
    }
}