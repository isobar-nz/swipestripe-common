<?php
declare(strict_types=1);

namespace SwipeStripe\Common\Product\ComplexProduct;

use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\ORM\DataList;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\DataQuery;
use SilverStripe\ORM\FieldType\DBBoolean;
use SilverStripe\ORM\FieldType\DBHTMLText;
use SilverStripe\ORM\ManyManyThroughList;
use SilverStripe\ORM\Relation;
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
 * @property int          $ProductID
 * @property bool         $OutOfStock
 * @property-read DBPrice $Price
 * @property-read string  $Description
 * @property-read string  $OptionsSummary
 * @method ComplexProduct Product()
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
    private static $db = [
        'OutOfStock' => 'Boolean',
    ];

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
        'OptionsSummary'  => 'Options',
        'Price.Value'     => 'Price',
        'IsComplete.Nice' => 'Complete',
    ];

    /**
     * @var array
     */
    private static $searchable_fields = [
        'ProductAttributeOptions.Title',
        'IsComplete',
    ];

    /**
     * @param ComplexProduct                                        $product
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
     * @param int[]               $optionIDs
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
            $variationOptionIDs = array_map('intval', $variationOptionIDs);

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
        $title = $this->Product()->Title;
        $prependOptions = [];
        $appendOptions = [];

        foreach ($this->ProductAttributeOptions() as $option) {
            if ($option->ProductAttribute()->AddToProductTitle) {
                if ($option->ProductAttribute()->PrependProductTitle) {
                    $prependOptions[] = $option->Title;
                } else {
                    $appendOptions[] = $option->Title;
                }
            }
        }

        if (!empty($prependOptions)) {
            $options = implode(' ', $prependOptions);
            $title = "{$options} {$title}";
        }

        if (!empty($appendOptions)) {
            $options = implode(' ', $appendOptions);
            $title = "{$title} ({$options})";
        }

        return $title;
    }

    /**
     * @inheritDoc
     */
    public function getDescription(): DBHTMLText
    {
        return $this->renderWith($this->getViewerTemplates('_Description'));
    }

    /**
     * @inheritDoc
     */
    public function getBasePrice(): DBPrice
    {
        $basePrice = $this->Product()->BasePrice->getMoney();

        foreach ($this->ProductAttributeOptions() as $option) {
            $basePrice = $basePrice->add($option->PriceModifier->getMoney());
        }

        return DBPrice::create_field(DBPrice::INJECTOR_SPEC, $basePrice);
    }

    /**
     * @inheritDoc
     */
    public function getPrice(): DBPrice
    {
        $basePrice = $this->getBasePrice();
        $this->extend('updatePrice', $basePrice);
        return $basePrice;
    }

    /**
     * @param null|string $action
     * @return string
     */
    public function Link(?string $action = null): string
    {
        return $this->Product()->Link($action);
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
     * @codeCoverageIgnore
     */
    public function getCMSFields()
    {
        $this->beforeUpdateCMSFields(function (FieldList $fields) {
            $fields->insertAfter('ProductID',
                PriceField::create('ProductBasePrice')
                    ->setValue($this->Product()->BasePrice)
                    ->setReadonly(true));
            $fields->insertAfter('ProductBasePrice',
                PriceField::create('VariationPrice')
                    ->setValue($this->Price)
                    ->setReadonly(true));
            $fields->insertAfter('VariationPrice', $this->dbObject('OutOfStock')->scaffoldFormField());

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
            $existingSearchButton->setSearchList($this->getOptionsForUnselectedAttributes());

            $options->setConfig($config);
        });

        return parent::getCMSFields();
    }

    /**
     * @param SS_List|ProductAttributeOption[]|null $selectedOptions
     * @return DataList|ProductAttributeOption[]
     */
    public function getOptionsForUnselectedAttributes(?SS_List $selectedOptions = null): DataList
    {
        $selectedOptions = $selectedOptions ?? $this->ProductAttributeOptions();
        $options = ProductAttributeOption::get()->filter('ProductAttribute.ProductID', $this->ProductID);

        $selectedAttributeIds = $selectedOptions->column('ProductAttributeID');
        if (!empty($selectedAttributeIds)) {
            $options = $options->filter('ProductAttributeID:not', $selectedAttributeIds);
        }

        return $options;
    }

    /**
     * @return Relation|ManyManyThroughList|ProductAttributeOption[]
     */
    public function ProductAttributeOptions(): Relation
    {
        return $this->getManyManyComponents('ProductAttributeOptions')
            ->sort('ProductAttribute.Sort', 'ASC');
    }

    /**
     * @return bool
     */
    public function IsOutOfStock(): bool
    {
        $outOfStock = boolval($this->OutOfStock);

        $this->extend('IsOutOfStock', $outOfStock);
        return $outOfStock;
    }

    /**
     * @return DBBoolean
     */
    public function IsComplete(): DBBoolean
    {
        $complete = !$this->getOptionsForUnselectedAttributes()->exists();

        $this->extend('IsComplete', $complete);
        return DBBoolean::create_field('Boolean', $complete);
    }

    public function write($showDebug = false, $forceInsert = false, $forceWrite = false, $writeComponents = false)
    {
        // All writes are forced for complex products - cart items always use modified date
        // to version nested relations (e.g. attributes / options) so we need to bump up this
        // date on every cms action
        return parent::write($showDebug, $forceInsert, true, $writeComponents);
    }
}
