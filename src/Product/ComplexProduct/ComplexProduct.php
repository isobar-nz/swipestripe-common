<?php
declare(strict_types=1);

namespace SwipeStripe\Common\Product\ComplexProduct;

use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;
use SilverStripe\Forms\GridField\GridFieldDetailForm;
use SilverStripe\ORM\HasManyList;
use SwipeStripe\Common\Product\ComplexProduct\CMS\VariationGridFieldItemRequest;
use SwipeStripe\Common\Product\ProductCMSPermissions;
use SwipeStripe\Price\DBPrice;
use Symbiote\GridFieldExtensions\GridFieldOrderableRows;

/**
 * Class ComplexProduct
 * @package SwipeStripe\Common\Product\ComplexProduct
 * @property string $Description
 * @property DBPrice $BasePrice
 * @property bool $OutOfStock
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
        'OutOfStock'  => 'Boolean',
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
        'ProductVariations',
    ];

    /**
     * @inheritdoc
     * @codeCoverageIgnore
     */
    public function getCMSFields()
    {
        $this->beforeUpdateCMSFields(function (FieldList $fields) {
            $fields->insertAfter('MenuTitle', $this->obj('Description')->scaffoldFormField());
            $fields->insertAfter('Description', $this->obj('BasePrice')->scaffoldFormField());
            $fields->insertAfter('BasePrice', $this->obj('OutOfStock')->scaffoldFormField());

            $attributesConfig = GridFieldConfig_RecordEditor::create();
            $attributesConfig->addComponent(GridFieldOrderableRows::create());

            $fields->addFieldToTab('Root.ProductAttributes',
                GridField::create('ProductAttributes', null, $this->ProductAttributes(),
                    $attributesConfig));

            $variationsConfig = GridFieldConfig_RecordEditor::create();
            /** @var GridFieldDetailForm $variationsDetailForm */
            $variationsDetailForm = $variationsConfig->getComponentByType(GridFieldDetailForm::class);
            $variationsDetailForm->setItemRequestClass(VariationGridFieldItemRequest::class);

            $fields->addFieldToTab('Root.ProductVariations',
                GridField::create('ProductVariations', null, $this->ProductVariations(), $variationsConfig));
        });

        return parent::getCMSFields();
    }

    /**
     * @return bool
     */
    public function IsOutOfStock(): bool
    {
        // Out of stock if flagged or there's no variations
        $outOfStock = boolval($this->OutOfStock) || !$this->ProductVariations()->exists();

        if (!$outOfStock) {
            // If not marked out of stock, check if any variations are not out of stock
            $outOfStock = true;
            foreach ($this->ProductVariations() as $variation) {
                if (!$variation->IsOutOfStock()) {
                    $outOfStock = false;
                    break;
                }
            }
        }

        $this->extend('IsOutOfStock', $outOfStock);
        return $outOfStock;
    }
}
