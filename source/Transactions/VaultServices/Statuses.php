<?php

namespace Spiral\Transactions\VaultServices;

use Spiral\Core\Service;
use Spiral\Transactions\Database\Types\TransactionStatus;
use Spiral\Translator\Traits\TranslatorTrait;

class Statuses extends Service
{
    use TranslatorTrait;

    const EXCLUDE = [
        TransactionStatus::FAILED
    ];

    const ICONS = [
        TransactionStatus::PENDING            => 'schedule',
        TransactionStatus::COMPLETED          => 'done',
        TransactionStatus::PARTIALLY_REFUNDED => 'replay',
        TransactionStatus::REFUNDED           => 'replay',
    ];

    /**
     * @param bool $placeholder
     *
     * @return array
     */
    public function labels(bool $placeholder = false): array
    {
        $labels = [];
        foreach (TransactionStatus::VALUES as $label) {
            if (!in_array($label, self::EXCLUDE)) {
                $labels[$label] = $this->makeLabel($label);
            }
        }

        if (!empty($placeholder)) {
            return [null => $this->say('All transactions')] + $labels;
        } else {
            return $labels;
        }
    }

    /**
     * @return array
     */
    public function icons(): array
    {
        return self::ICONS;
    }

    /**
     * @param string $status
     *
     * @return string
     */
    public function icon(string $status): string
    {
        return self::ICONS[$status];
    }

    /**
     * @param string $label
     *
     * @return string
     */
    private function makeLabel(string $label): string
    {
        return $this->say(ucwords(str_replace('-', ' ', $label)));
    }

    /**
     * Get label for status.
     *
     * @param string $status
     *
     * @return string
     */
    public function label(string $status): string
    {
        return $this->makeLabel($status);
    }

    /**
     * If status is listed
     *
     * @param string $status
     *
     * @return bool
     */
    public function isListed(string $status): bool
    {
        return array_key_exists($status, $this->labels);
    }
}