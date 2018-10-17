<?php
declare(strict_types=1);

namespace SwipeStripe\Common\Product\ComplexProduct;

use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\HasManyList;
use SilverStripe\Versioned\Versioned;
use SwipeStripe\Common\Product\ProductCMSPermissions;
use Symbiote\GridFieldExtensions\GridFieldOrderableRows;

/**
 * Class ProductAttribute
 * @package SwipeStripe\Common\Product\ComplexProduct
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
        'Title' => 'Varchar',
        'Sort'  => 'Int',
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
     * @inheritdoc
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
        });

        return parent::getCMSFields();
    }
}
