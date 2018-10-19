<?php
declare(strict_types=1);

namespace SwipeStripe\Common\Product\ComplexProduct\CartForm;

use SilverStripe\Forms\DropdownField;
use SwipeStripe\Common\Product\ComplexProduct\ProductAttribute;

/**
 * Class ProductAttributeField
 * @package SwipeStripe\Common\Product\ComplexProduct\CartForm
 */
class ProductAttributeField extends DropdownField
{
    /**
     * @var ProductAttribute
     */
    protected $productAttribute;

    /**
     * ProductAttributeField constructor.
     * @param ProductAttribute $attribute
     * @param string $name
     * @param null|string $title
     * @param null|string $value
     */
    public function __construct(ProductAttribute $attribute, string $name, ?string $title = null, $value = null)
    {
        $this->productAttribute = $attribute;
        parent::__construct($name, $title,
            $attribute->ProductAttributeOptions()->map('ID', 'DropdownTitle'),
            $value);
    }

    /**
     * @return ProductAttribute
     */
    public function getProductAttribute(): ProductAttribute
    {
        return $this->productAttribute;
    }
}
