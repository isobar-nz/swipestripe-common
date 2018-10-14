<?php
declare(strict_types=1);

namespace SwipeStripe\Common\Product\ComplexProduct;

use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\ManyManyList;
use SilverStripe\Versioned\Versioned;
use SwipeStripe\Common\Product\ProductCMSPermissions;
use SwipeStripe\Order\PurchasableInterface;
use SwipeStripe\Price\DBPrice;

/**
 * Class ComplexProductVariation
 * @package SwipeStripe\Common\Product\ComplexProduct
 * @property int $ProductID
 * @property-read string $OptionsSummary
 * @method ComplexProduct Product()
 * @method ManyManyList|ProductAttributeOption[] ProductAttributeOptions()
 * @mixin Versioned
 */
class ComplexProductVariation extends DataObject implements PurchasableInterface
{
    use ProductCMSPermissions;

    /**
     * @var string
     */
    private static $table_name = 'SwipeStripe_Common_ComplexProductVariation';

    /**
     * @var array
     */
    private static $has_one = [
        'Product' => ComplexProduct::class,
    ];

    /**
     * @var array
     */
    private static $many_many = [
        'ProductAttributeOptions' => [
            'through' => ComplexProductVariation_Options::class,
            'from'    => 'ComplexProductVariation',
            'to'      => 'ProductAttributeOption',
        ],
    ];

    /**
     * @var array
     */
    private static $extensions = [
        Versioned::class => Versioned::class . '.versioned',
    ];

    /**
     * @var array
     */
    private static $summary_fields = [
        'OptionsSummary' => 'Options',
        'Price.Nice'     => 'Price',
    ];

    /**
     * @var array
     */
    private static $searchable_fields = [
        'ProductAttributeOptions.Title',
    ];

    /**
     * @inheritDoc
     */
    public function getTitle()
    {
        return $this->Product()->Title;
    }

    /**
     * @inheritDoc
     */
    public function getDescription(): string
    {
        return $this->Product()->Description ?? $this->Product()->obj('Content')->Summary() ?? '';
    }

    /**
     * @inheritDoc
     */
    public function getPrice(): DBPrice
    {
        $basePrice = $this->Product()->BasePrice->getMoney();

        foreach ($this->ProductAttributeOptions() as $option) {
            $basePrice = $basePrice->add($option->PriceModifier->getMoney());
        }

        return DBPrice::create_field(DBPrice::INJECTOR_SPEC, $basePrice);
    }

    /**
     * @return string
     */
    public function getOptionsSummary(): string
    {
        $options = [];

        foreach ($this->ProductAttributeOptions() as $option) {
            $options[] = "{$option->ProductAttribute()->Title}: {$option->Title}";
        }

        return implode('; ', $options);
    }
}
