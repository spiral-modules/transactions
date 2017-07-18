<?php

namespace Spiral\Transactions\VaultServices;

use Spiral\Transactions\Configs\CurrencyConfig;

class Currencies
{
    /** @var \Spiral\Transactions\Configs\CurrencyConfig */
    private $config;

    /**
     * Currencies constructor.
     *
     * @param \Spiral\Transactions\Configs\CurrencyConfig $config
     */
    public function __construct(CurrencyConfig $config)
    {
        $this->config = $config;
    }

    /**
     * @param string     $currency
     * @param float|null $amount
     *
     * @return string
     */
    public function formatValue(string $currency, $amount = null): string
    {
        $amount = $this->formatAmount($currency, $amount);
        $options = $this->getOptions($currency, $amount);

        return l($this->getFormat($currency), $options);
    }

    /**
     * @param string                  $currency
     * @param float|int|double|string $amount
     *
     * @return string
     */
    private function formatAmount(string $currency, $amount)
    {
        $amount = $amount / $this->getMultiplier($currency);
        $sprintf = $this->getFormatAmount($currency);

        if (!empty($sprintf)) {
            $amount = sprintf($sprintf, $amount);
        }

        return $amount;
    }

    /**
     * @param string $currency
     *
     * @return int
     */
    private function getMultiplier(string $currency): int
    {
        if ($this->config->isSupported($currency)) {
            return $this->config->getMultiplier($currency);
        } else {
            return $this->config->getMultiplier(CurrencyConfig::DEFAULT_CURRENCY);
        }
    }

    /**
     * @param string $currency
     *
     * @return null|string
     */
    private function getFormatAmount(string $currency)
    {
        if ($this->config->isSupported($currency)) {
            return $this->config->getFormatAmount($currency);
        } else {
            return $this->config->getFormatAmount(CurrencyConfig::DEFAULT_CURRENCY);
        }
    }

    /**
     * @param string $currency
     * @param        $amount
     *
     * @return array
     */
    private function getOptions(string $currency, $amount): array
    {
        if ($this->config->isSupported($currency)) {
            $options = $this->config->getOptions($currency);
            $options['amount'] = $amount;
        } else {
            $options = [
                'code'   => $currency,
                'amount' => $amount
            ];
        }

        return $options;
    }

    /**
     * @param string $currency
     *
     * @return string
     */
    private function getFormat(string $currency): string
    {
        if ($this->config->isSupported($currency)) {
            return $this->config->getFormat($currency);
        } else {
            return $this->config->getFormat(CurrencyConfig::DEFAULT_CURRENCY);
        }
    }
}