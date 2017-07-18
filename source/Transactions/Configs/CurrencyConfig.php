<?php

namespace Spiral\Transactions\Configs;

use Spiral\Core\InjectableConfig;

class CurrencyConfig extends InjectableConfig
{
    const DEFAULT_CURRENCY = 'default-currency';
    const CONFIG           = 'modules/currencies';

    protected $config = [
        'currencies' => [
            'usd'              => [
                'code'       => 'usd',
                'char'       => '$',
                'multiplier' => 100,
                'format'     => '{char}{amount}',
                'sprintf'    => '%.2f'
            ],
            'default-currency' => [
                'format'     => '{code}{amount}',
                'sprintf'    => '%.2f',
                'multiplier' => 1
            ]
        ],
        'defaults'   => [
            'multiplier' => 1,
            'sprintf'    => null
        ]
    ];

    /**
     * @param string $currency
     *
     * @return bool
     */
    public function isSupported(string $currency): bool
    {
        return !empty($this->config['currencies'][$currency]);
    }

    /**
     * @param string $currency
     *
     * @return string
     */
    public function getChar(string $currency): string
    {
        return $this->config['currencies'][$currency]['char'];
    }

    /**
     * Multiplier is an optional, default (1) will be used, if currency doesn't have it.
     *
     * @param string $currency
     *
     * @return int
     */
    public function getMultiplier(string $currency): int
    {
        if (!empty($this->config['currencies'][$currency]['multiplier'])) {
            return $this->config['currencies'][$currency]['multiplier'];
        }

        return $this->config['defaults']['multiplier'];
    }

    /**
     * @param string $currency
     *
     * @return string
     */
    public function getFormat(string $currency): string
    {
        return $this->config['currencies'][$currency]['format'];
    }

    /**
     * Amount formatter is an optional, default (null) will be used, if currency doesn't have it.
     * @param string $currency
     *
     * @return string|null
     */
    public function getFormatAmount(string $currency)
    {
        if (!empty($this->config['currencies'][$currency]['sprintf'])) {
            return $this->config['currencies'][$currency]['sprintf'];
        }

        return $this->config['defaults']['sprintf'];
    }

    /**
     * @param string $currency
     *
     * @return array
     */
    public function getOptions(string $currency): array
    {
        return $this->config['currencies'][$currency];
    }
}