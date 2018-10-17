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

        $product = $this->ComplexProductVariation()->Product();
        $totalAttributes = $product->ProductAttributes()->count();
        $options = $this->ComplexProductVariation()->ProductAttributeOptions()->column();
        $options[] = $this->ProductAttributeOptionID; // With the prospective relation we're creating

        // Only check for dupes if this would be a complete variation
        if (count($options) === $totalAttributes) {
            $potentialDuplicate = ComplexProductVariation::getVariationWithExactOptions($product, $options);
            if ($potentialDuplicate !== null && $potentialDuplicate->ID !== $this->ComplexProductVariationID) {
                $result->addError('Adding that option would create a duplicate of an existing variation ' .
                    "(ID {$potentialDuplicate->ID}).");
            }
        }

        return $result;
    }
}
