<?php
/**
 * Created by PhpStorm.
 * User: Valentin
 * Date: 06.07.2017
 * Time: 14:56
 */

namespace Spiral\Transactions\Database\Types;


use Spiral\ORM\Columns\EnumColumn;

class RevisionOperation extends EnumColumn
{
    const PURCHASE = 'purchase';
    const REFUND   = 'refund';

    const VALUES  = [self::PURCHASE, self::REFUND];
    const DEFAULT = self::PURCHASE;
}