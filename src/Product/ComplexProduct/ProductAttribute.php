<?php
declare(strict_types=1);

namespace SwipeStripe\Common\Product\ComplexProduct;

use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\HasManyList;
use SilverStripe\Versioned\Versioned;
use SwipeStripe\Common\Product\ProductCMSPermissions;
use Symbiote\GridFieldExtensions\GridFieldOrderableRows;
use UncleCheese\DisplayLogic\Extensions\DisplayLogic;

/**
 * Class ProductAttribute
 * @package SwipeStripe\Common\Product\ComplexProduct
 * @property int $Sort
 * @property bool $AddToProductTitle
 * @property bool $PrependProductTitle
 * @property int $ProductID
 * @method ComplexProduct Product()
 * @method HasManyList|ProductAttributeOption[] ProductAttributeOptions()
 * @mixin Versioned
 */
class ProductAttribute extends DataObject
{
    use ProductCMSPermissions;

    /**
     * @var string
     */
    private static $table_name = 'SwipeStripe_Common_ComplexProductAttribute';

    /**
     * @var array
     */
    private static $db = [
        'Title'               => 'Varchar',
        'Sort'                => 'Int',
        'AddToProductTitle'   => 'Boolean',
        'PrependProductTitle' => 'Boolean',
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
    private static $has_many = [
        'ProductAttributeOptions' => ProductAttributeOption::class,
    ];

    /**
     * @var string
     */
    private static $default_sort = '"Sort" ASC';

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
        Versioned::class => Versioned::class,
    ];

    /**
     * @var array
     */
    private static $defaults = [
        'AddToProductTitle' => true,
    ];

    /**
     * @inheritdoc
     * @codeCoverageIgnore
     */
    public function getCMSFields()
    {
        $this->beforeUpdateCMSFields(function (FieldList $fields) {
            $optionsGridField = $fields->dataFieldByName('ProductAttributeOptions');

            if ($optionsGridField instanceof GridField) {
                $config = GridFieldConfig_RecordEditor::create();
                $config->addComponent(GridFieldOrderableRows::create());

                $optionsGridField->setConfig($config);
            }

            $fields->dataFieldByName('AddToProductTitle')
                ->setDescription("If enabled, the option name will be added to the product's title in " .
                    'views wherever an option for this attribute has been selected (e.g. cart, receipt).');

            /** @var CheckboxField|DisplayLogic $prependField */
            $prependField = $fields->dataFieldByName('PrependProductTitle');
            $prependField->setDescription('If enabled, the option name will be added to the beginning of ' .
                'the product title. If disabled, it will be appended in brackets. E.g. "PrependedOne PrependedTwo ' .
                'PRODUCT TITLE (AppendedOne AppendedTwo)"');
            $prependField->displayUnless('AddToProductTitle')->isNotChecked();
        });

        return parent::getCMSFields();
    }
}
