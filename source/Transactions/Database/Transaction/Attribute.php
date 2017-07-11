<?php

namespace Spiral\Transactions\Database\Transaction;

use Spiral\ORM\Record;

/**
 * Class Attribute
 *
 * @property string $name
 * @property string $value
 * @package Spiral\Transactions\Database\Transaction
 */
class Attribute extends Record
{
    const DATABASE = 'transactions';

    const SCHEMA = [
        'id'    => 'primary',
        'name'  => 'string(128)',
        'value' => 'string(255)'
    ];

    const INDEXES = [
        [self::INDEX, 'name', 'value']
    ];

    const FILLABLE = ['*'];

    const IP_ADDRESS    = 'ipAddress';
    const GATEWAY_ERROR = 'gatewayError';

    public function getName(): string
    {
        return $this->name;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}