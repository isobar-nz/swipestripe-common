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

    /**
     * @inheritDoc
     */
    public function validate()
    {
        $result = parent::validate();

        /** @var static|null $existingAttributeThrough */
        $existingAttributeThrough = static::get()->filter([
            'ComplexProductVariationID'                 => $this->ComplexProductVariationID,
            'ProductAttributeOption.ProductAttributeID' => $this->ProductAttributeOption()->ProductAttributeID,
        ])->first();

        if ($existingAttributeThrough !== null) {
            $existingAttributeOption = $existingAttributeThrough->ProductAttributeOption();
            $result->addError("An option for the {$existingAttributeOption->ProductAttribute()->Title} " .
                "attribute is already selected ({$existingAttributeOption->Title}) in this variation.");
        }

        return $result;
    }
}
