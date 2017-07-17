<?php

namespace Spiral\Transactions\Processors\PaymentProcessor;

use Spiral\Transactions\Database\Transaction;
use Spiral\Transactions\Database\Transaction\Item;

class Metadata
{
    /**
     * @param array       $params
     * @param Transaction $transaction
     *
     * @return array
     */
    public function mergeMetadata(array $params, Transaction $transaction): array
    {
        $types = [];
        $output = [];
        foreach ($transaction->items as $item) {
            $type = $item->getType();
            $types = $this->countType($types, $type);

            $output[$this->makeItemKey($types, $type)] = $this->makeItemValue($item);
        }

        return $this->merge($params, $output);
    }

    /**
     * @param array $params
     * @param array $metadata
     *
     * @return array
     */
    protected function merge(array $params, array $metadata): array
    {
        $params['metadata'] += $metadata;

        return $params;
    }

    /**
     * Increment type counter, return updated array.
     *
     * @param array  $types
     * @param string $type
     *
     * @return array
     */
    private function countType(array $types, string $type): array
    {
        if (empty($types[$type])) {
            $types[$type] = 1;
        } else {
            $types[$type]++;
        }

        return $types;
    }

    /**
     * @param array  $types
     * @param string $type
     *
     * @return string
     */
    private function makeItemKey(array $types, string $type): string
    {
        if (empty($types[$type])) {
            return $type;
        }

        return $type . ':' . $types[$type];
    }

    /**
     * @param Item $item
     *
     * @return string
     */
    private function makeItemValue(Item $item): string
    {
        return sprintf(
            '%s (%s x %s)',
            $item->title,
            $item->quantity,
            number_format($item->getAmount(), 2)
        );
    }
}