<?php
declare(strict_types=1);

namespace SwipeStripe\Common\Product\ComplexProduct;

use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldAddExistingAutocompleter;
use SilverStripe\Forms\GridField\GridFieldAddNewButton;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\ManyManyList;
use SilverStripe\Versioned\Versioned;
use SwipeStripe\Common\Product\ProductCMSPermissions;
use SwipeStripe\Price\DBPrice;

/**
 * Class ProductAttributeOption
 * @package SwipeStripe\Common\Product\ComplexProduct
 * @property-read string $DropdownTitle
 * @property DBPrice $PriceModifier
 * @property int $Sort
 * @property int $ProductAttributeID
 * @method ProductAttribute ProductAttribute()
 * @method ManyManyList|ComplexProductVariation[] ProductVariations()
 * @mixin Versioned
 */
class ProductAttributeOption extends DataObject
{
    use ProductCMSPermissions;

    /**
     * @var string
     */
    private static $table_name = 'SwipeStripe_Common_ComplexProductAttributeOption';

    /**
     * @var array
     */
    private static $db = [
        'Title'         => 'Varchar',
        'PriceModifier' => 'Price',
        'Sort'          => 'Int',
    ];

    /**
     * @var array
     */
    private static $has_one = [
        'ProductAttribute' => ProductAttribute::class,
    ];

    /**
     * @var array
     */
    private static $belongs_many_many = [
        'ProductVariations' => ComplexProductVariation::class . '.ProductAttributeOptions',
    ];

    /**
     * @var array
     */
    private static $extensions = [
        Versioned::class => Versioned::class,
    ];

    /**
     * @var string
     */
    private static $default_sort = '"Sort" ASC';

    /**
     * @var array
     */
    private static $summary_fields = [
        'ProductAttribute.Title' => 'Attribute',
        'Title'                  => 'Option',
        'PriceModifier.Value'    => 'Price Modifier',
    ];

    /**
     * @var array
     */
    private static $searchable_fields = [
        'Title',
        'ProductAttributeID' => [
            'title' => 'Product Attribute'
        ],
    ];

    /**
     * @return string
     */
    public function getDropdownTitle(): string
    {
        $priceModifierMoney = $this->PriceModifier->getMoney();
        if ($priceModifierMoney->isZero()) {
            return $this->Title;
        }

        $prefix = $priceModifierMoney->isPositive() ? '+' : '';
        return "{$this->Title} ({$prefix}{$this->PriceModifier->Nice()})";
    }

    /**
     * @inheritdoc
     */
    public function getCMSFields()
    {
        $this->beforeUpdateCMSFields(function (FieldList $fields) {
            $fields->removeByName('Sort');

            $fields->dataFieldByName('PriceModifier')
                ->setDescription('This value will be added to the base price (negatives to reduce base ' .
                    'price).');

            $productVariations = $fields->dataFieldByName('ProductVariations');
            if ($productVariations instanceof GridField) {
                $productVariations->setConfig(
                    GridFieldConfig_RecordEditor::create()->removeComponentsByType([
                        GridFieldAddNewButton::class,
                        GridFieldAddExistingAutocompleter::class,
                    ])
                );
            }
        });

        return parent::getCMSFields();
    }
}
