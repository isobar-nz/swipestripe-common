<?php
declare(strict_types=1);

namespace SwipeStripe\Common\Product\ComplexProduct;

use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;
use SilverStripe\ORM\HasManyList;
use SwipeStripe\Common\Product\ProductCMSPermissions;
use SwipeStripe\Price\DBPrice;
use Symbiote\GridFieldExtensions\GridFieldOrderableRows;

/**
 * Class ComplexProduct
 * @package SwipeStripe\Common\Product\ComplexProduct
 * @property string $Description
 * @property DBPrice $BasePrice
 * @method HasManyList|ProductAttribute[] ProductAttributes()
 * @method HasManyList|ComplexProductVariation[] ProductVariations()
 */
class ComplexProduct extends \Page
{
    use ProductCMSPermissions;

    /**
     * @var string
     */
    private static $table_name = 'SwipeStripe_Common_ComplexProduct';

    /**
     * @var array
     */
    private static $db = [
        'Description' => 'Text',
        'BasePrice'   => 'Price',
    ];

    /**
     * @var array
     */
    private static $has_many = [
        'ProductAttributes' => ProductAttribute::class,
        'ProductVariations' => ComplexProductVariation::class,
    ];

    /**
     * @var array
     */
    private static $owns = [
        'ProductAttributes',
    ];

    /**
     * @inheritdoc
     */
    public function getCMSFields()
    {
        $this->beforeUpdateCMSFields(function (FieldList $fields) {
            $fields->insertAfter('MenuTitle', $this->obj('Description')->scaffoldFormField());
            $fields->insertAfter('Description', $this->obj('BasePrice')->scaffoldFormField());

            $attributesConfig = GridFieldConfig_RecordEditor::create();
            $attributesConfig->addComponent(GridFieldOrderableRows::create());

            $fields->addFieldToTab('Root.ProductAttributes',
                GridField::create('ProductAttributes', null, $this->ProductAttributes(),
                    $attributesConfig));

            $fields->addFieldToTab('Root.ProductVariations',
                GridField::create('ProductVariations', null, $this->ProductVariations(),
                    GridFieldConfig_RecordEditor::create()));
        });

        return parent::getCMSFields();
    }
}
