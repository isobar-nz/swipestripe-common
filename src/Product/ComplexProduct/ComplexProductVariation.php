<?php
declare(strict_types=1);

namespace SwipeStripe\Common\Product\ComplexProduct;

use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\ORM\DataList;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\DataQuery;
use SilverStripe\ORM\ManyManyThroughList;
use SilverStripe\ORM\SS_List;
use SilverStripe\Versioned\Versioned;
use SwipeStripe\Common\Product\ComplexProduct\CMS\GridFieldConfig_VariationOptionsEditor;
use SwipeStripe\Common\Product\ProductCMSPermissions;
use SwipeStripe\Order\PurchasableInterface;
use SwipeStripe\Price\DBPrice;
use SwipeStripe\Price\PriceField;
use Symbiote\GridFieldExtensions\GridFieldAddExistingSearchButton;

/**
 * Class ComplexProductVariation
 * @package SwipeStripe\Common\Product\ComplexProduct
 * @property int $ProductID
 * @property DBPrice $Price
 * @property string $Description
 * @property-read string $OptionsSummary
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
    private static $owns = [
        'ProductAttributeOptions',
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
     * @param ComplexProduct $product
     * @param array|ProductAttributeOption[]|int[]|SS_List|iterable $options
     * @return DataList|static[]
     */
    public static function getVariationsWithOptions(ComplexProduct $product, iterable $options): DataList
    {
        return static::get()
            ->filter([
                'ProductID'                  => $product->ID,
                'ProductAttributeOptions.ID' => $options,
            ])
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
     * @param null|ComplexProduct $product
     * @return null|static
     */
    public static function getVariationWithExactOptions(ComplexProduct $product, array $optionIDs): ?self
    {
        sort($optionIDs);
        $variations = static::getVariationsWithOptions($product, $optionIDs);

        foreach ($variations as $variation) {
            $variationOptionIDs = $variation->ProductAttributeOptions()
                ->sort('ID')
                ->column('ID');

            if ($variationOptionIDs === $optionIDs) {
                return $variation;
            }
        }

        return null;
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
        $description = $this->Product()->Description ?? $this->Product()->obj('Content')->Summary() ?? '';

        foreach ($this->ProductAttributeOptions() as $option) {
            if (!empty($description)) {
                $description .= "\n";
            }

            $description .= "{$option->ProductAttribute()->Title}: {$option->Title}";
        }

        return $description;
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

    /**
     * @inheritdoc
     */
    public function getCMSFields()
    {
        $this->beforeUpdateCMSFields(function (FieldList $fields) {
            $fields->insertAfter('ProductID',
                PriceField::create('ProductBasePrice', null)
                    ->setValue($this->Product()->BasePrice));
            $fields->insertAfter('ProductBasePrice',
                PriceField::create('VariationPrice', null)
                    ->setValue($this->Price));

            $options = $fields->dataFieldByName('ProductAttributeOptions');
            if (!$options instanceof GridField) {
                return;
            }

            $fields->remove($options);
            $fields->removeFieldFromTab('Root', 'ProductAttributeOptions');
            $fields->addFieldToTab('Root.Main', $options);

            $config = GridFieldConfig_VariationOptionsEditor::create();
            /** @var GridFieldAddExistingSearchButton $existingSearchButton */
            $existingSearchButton = $config->getComponentByType(GridFieldAddExistingSearchButton::class);

            // Hide options where attribute already has a selected value (e.g. hide colour options if variation already
            // has "Red")
            $selectedOptionIds = $this->ProductAttributeOptions()->column('ProductAttributeID');
            if (!empty($selectedOptionIds)) {
                $existingSearchButton->setSearchList(ProductAttributeOption::get()
                    ->filter('ProductAttributeID:not', $selectedOptionIds));
            }

            $options->setConfig($config);
        });

        return parent::getCMSFields();
    }
}
