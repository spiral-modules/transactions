<?php

namespace Spiral\Transactions\VaultServices;

class Currencies
{
    /**
     * todo add config with currencies chars
     *
     * @param float|null $amount
     * @param string     $currency
     *
     * @return string
     */
    public function formatValue(float $amount = null, string $currency): string
    {
        return number_format($amount, 2) . $currency;
    }
}