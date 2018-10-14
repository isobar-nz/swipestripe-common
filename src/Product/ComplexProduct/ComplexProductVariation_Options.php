<?php
declare(strict_types=1);

namespace SwipeStripe\Common\Product\ComplexProduct;

use SilverStripe\ORM\DataObject;

/**
 * Class ComplexProductVariation_Options
 * @package SwipeStripe\Common\Product\ComplexProduct
 * @property int $ComplexProductVariationID
 * @property int $ProductAttributeOptionID
 * @method ComplexProductVariation ComplexProductVariation()
 * @method ProductAttributeOption ProductAttributeOption()
 */
class ComplexProductVariation_Options extends DataObject
{
    /**
     * @var string
     */
    private static $table_name = 'SwipeStripe_Common_ComplexProductVariation_Options';

    /**
     * @var array
     */
    private static $has_one = [
        'ComplexProductVariation' => ComplexProductVariation::class,
        'ProductAttributeOption'  => ProductAttributeOption::class,
    ];
}
