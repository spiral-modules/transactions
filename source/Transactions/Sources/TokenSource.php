<?php

namespace Spiral\Transactions\Sources;

final class TokenSource
{
    /** @var string */
    protected $sourceID;

    /** @var string|null */
    protected $customerID;

    /**
     * TokenSource constructor.
     *
     * @param string      $sourceID
     * @param string|null $customerID
     */
    public function __construct(string $sourceID, string $customerID = null)
    {
        $this->customerID = $customerID;
        $this->sourceID = $sourceID;
    }

    /**
     * Gateway internal card ID - card should be stored in the payment gateway system.
     *
     * @return string
     */
    public function getSourceID(): string
    {
        return $this->sourceID;
    }

    /**
     * Gateway internal customer ID (if exists) - customer should be stored in the payment gateway system.
     *
     * @return string|null
     */
    public function getCustomerID(): ?string
    {
        return $this->customerID;
    }
}