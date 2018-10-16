<?php
declare(strict_types=1);

namespace SwipeStripe\Common\Product\ComplexProduct;

use SilverStripe\ORM\DataList;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\DataQuery;
use SilverStripe\ORM\ManyManyThroughList;
use SilverStripe\ORM\SS_List;
use SilverStripe\Versioned\Versioned;
use SwipeStripe\Common\Product\ProductCMSPermissions;
use SwipeStripe\Order\PurchasableInterface;
use SwipeStripe\Price\DBPrice;

/**
 * Class ComplexProductVariation
 * @package SwipeStripe\Common\Product\ComplexProduct
 * @property int $ProductID
 * @property-read string $OptionsSummary
 * @property ComplexProduct $Product
 * @method ComplexProduct Product()
 * @method ManyManyThroughList|ProductAttributeOption[] ProductAttributeOptions()
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
     * @param array|ProductAttributeOption[]|int[]|SS_List|iterable $options
     * @return DataList|static[]
     * @throws \Exception
     */
    public static function getVariationsWithOptions(iterable $options): DataList
    {
        return static::get()
            ->filter('ProductAttributeOptions.ID', $options)
            ->alterDataQuery(function (DataQuery $query) use ($options) {
                $table = static::singleton()->baseTable();
                return $query->groupby('"ComplexProductVariationID"')
                    ->having([
                        "COUNT(\"{$table}\".\"ID\")" => count($options),
                    ]);
            });
    }

    /**
     * @param int[] $optionIDs
     * @param bool $createIfMissing
     * @param null|ComplexProduct $product
     * @return null|static
     */
    public static function getVariationWithExactOptions(
        array $optionIDs,
        bool $createIfMissing = false,
        ?ComplexProduct $product = null
    ): ?self {
        try {
            $variations = static::getVariationsWithOptions($optionIDs);
        } catch (\Exception $e) {
            return null;
        }

        sort($optionIDs);

        foreach ($variations as $variation) {
            $variationOptionIDs = $variation->ProductAttributeOptions()
                ->sort('ID')
                ->column('ID');

            if ($variationOptionIDs === $optionIDs) {
                return $variation;
            }
        }

        if (!$createIfMissing) {
            return null;
        } elseif ($product === null) {
            throw new \InvalidArgumentException('Product is required to create missing variation.');
        }

        $variation = static::create();
        $variation->Product = $product;
        $variation->write();
        $variation->ProductAttributeOptions()->setByIDList($optionIDs);

        return $variation;
    }

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
