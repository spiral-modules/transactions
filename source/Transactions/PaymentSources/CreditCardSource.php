<?php

namespace Spiral\Transactions\PaymentSources;

final class CreditCardSource
{
    /** @var string|null */
    protected $cardHolder;

    /** @var string */
    protected $cardNumber;

    /** @var int */
    protected $expMonth;

    /** @var int */
    protected $expYear;

    /** @var int|null */
    protected $securityCode;

    /**
     * CreditCardSource constructor.
     *
     * @param string      $cardNumber
     * @param int         $expMonth
     * @param int         $expYear
     * @param string|null $cardHolder
     * @param int|null    $securityCode
     */
    public function __construct(string $cardNumber, int $expMonth, int $expYear, string $cardHolder = null, int $securityCode = null)
    {
        $this->cardNumber = $cardNumber;
        $this->expMonth = $expMonth;
        $this->expYear = $expYear;
        $this->cardHolder = $cardHolder;
        $this->securityCode = $securityCode;
    }

    /**
     * Card holder name (John Doe).
     *
     * @return string|null
     */
    public function getCardHolder(): ?string
    {
        return $this->cardHolder;
    }

    /**
     * Card number (xxxx xxxx xxxx xxxx).
     *
     * @return string
     */
    public function getCardNumber(): string
    {
        return $this->cardNumber;
    }

    /**
     * Card expiration month (12).
     *
     * @return int
     */
    public function getExpMonth(): int
    {
        return $this->expMonth;
    }

    /**
     * Card expiration year (2017).
     *
     * @return int
     */
    public function getExpYear(): int
    {
        return $this->expYear;
    }

    /**
     * Card security code (CVC).
     *
     * @return int|null
     */
    public function getSecurityCode(): ?int
    {
        return $this->securityCode;
    }
}