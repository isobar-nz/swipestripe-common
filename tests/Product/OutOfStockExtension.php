<?php
declare(strict_types=1);

namespace SwipeStripe\Common\Tests\Product;

use SilverStripe\ORM\DataExtension;

/**
 * Class OutOfStockExtension
 * @package SwipeStripe\Common\Tests\Product
 */
class OutOfStockExtension extends DataExtension
{
    /**
     * @param bool $outOfStock
     */
    public function IsOutOfStock(bool &$outOfStock)
    {
        $outOfStock = true;
    }
}
