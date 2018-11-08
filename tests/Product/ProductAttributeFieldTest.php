<?php
declare(strict_types=1);

namespace SwipeStripe\Common\Tests\Product;

use SwipeStripe\Common\Product\ComplexProduct\CartForm\ProductAttributeField;
use SwipeStripe\Common\Product\ComplexProduct\ProductAttribute;
use SwipeStripe\Common\Product\ComplexProduct\ProductAttributeOption;
use SwipeStripe\Common\Tests\BaseTest;
use SwipeStripe\Common\Tests\Fixtures\Fixtures;

/**
 * Class ProductAttributeFieldTest
 * @package SwipeStripe\Common\Tests\Product
 */
class ProductAttributeFieldTest extends BaseTest
{
    /**
     * @var array
     */
    protected static $fixture_file = [
        Fixtures::COMPLEX_PRODUCTS,
    ];

    /**
     * @var bool
     */
    protected $usesDatabase = true;

    /**
     *
     */
    public function testDropdownFieldShowsNonZeroPriceEffect()
    {
        /** @var ProductAttribute $size */
        $size = $this->objFromFixture(ProductAttribute::class, 'tshirt-size');
        $attributeField = ProductAttributeField::create($size, 'Size');
        $optionSource = $attributeField->getSource();

        foreach ($size->ProductAttributeOptions() as $option) {
            if (!$option->PriceModifier->getMoney()->isZero()) {
                $this->assertContains($option->PriceModifier->Nice(), $optionSource[$option->ID]);
            }
        }
    }

    /**
     * @inheritDoc
     */
    protected function setUp()
    {
        static::registerPublishingBlueprint(ProductAttribute::class);
        static::registerPublishingBlueprint(ProductAttributeOption::class);

        parent::setUp();
    }
}
