<?php
/**
 * Created by PhpStorm.
 * User: Valentin
 * Date: 06.07.2017
 * Time: 15:48
 */

namespace Spiral\Transactions\Database\Types;


use Spiral\ORM\Columns\EnumColumn;

class ItemType extends EnumColumn
{
    const ITEM     = 'item';
    const DISCOUNT = 'discount';

    const VALUES  = [self::ITEM, self::DISCOUNT];
    const DEFAULT = self::ITEM;

    /**
     * If current item is a purchased item (not a discount or another internal entity).
     *
     * @return bool
     */
    public function isItem(): bool
    {
        return $this->packValue() === self::ITEM;
    }
}