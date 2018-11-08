<?php
declare(strict_types=1);

namespace SwipeStripe\Common\Tests\Product;

use SwipeStripe\Common\Product\SimpleProduct;
use SwipeStripe\Common\Tests\BaseTest;
use SwipeStripe\Common\Tests\Fixtures\Fixtures;

/**
 * Class SimpleProductTest
 * @package SwipeStripe\Common\Tests\Product
 */
class SimpleProductTest extends BaseTest
{
    /**
     * @var array
     */
    protected static $fixture_file = [
        Fixtures::SIMPLE_PRODUCTS,
    ];

    /**
     * @var bool
     */
    protected $usesDatabase = true;

    /**
     * @var SimpleProduct
     */
    protected $knickKnack, $outOfStock;

    /**
     *
     */
    public function testIsOutOfStock()
    {
        $this->assertTrue($this->outOfStock->IsOutOfStock());
        $this->assertFalse($this->knickKnack->IsOutOfStock());
    }

    /**
     *
     */
    public function testIsOutOfStockExtension()
    {
        SimpleProduct::add_extension(OutOfStockExtension::class);

        $this->assertTrue($this->outOfStock->IsOutOfStock());
        $this->assertTrue($this->knickKnack->IsOutOfStock());
    }

    /**
     * @inheritDoc
     */
    protected function setUp()
    {
        static::registerPublishingBlueprint(SimpleProduct::class);

        parent::setUp();

        $this->knickKnack = $this->objFromFixture(SimpleProduct::class, 'knick-knack');
        $this->outOfStock = $this->objFromFixture(SimpleProduct::class, 'out-of-stock');
    }
}
