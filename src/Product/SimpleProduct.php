<?php
declare(strict_types=1);

namespace SwipeStripe\Common\Product;

use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\FieldType\DBHTMLText;
use SwipeStripe\Order\PurchasableInterface;
use SwipeStripe\Price\DBPrice;

/**
 * Class SimpleProduct
 * @package SwipeStripe\Common\Product
 * @property string $ShortDescription
 * @property string $Description
 * @property DBPrice $Price
 */
class SimpleProduct extends \Page implements PurchasableInterface
{
    use ProductCMSPermissions;

    /**
     * @var string
     */
    private static $table_name = 'SwipeStripe_Common_SimpleProduct';

    /**
     * @var array
     */
    private static $db = [
        'ShortDescription' => 'Text',
        'Price'            => 'Price',
    ];

    /**
     * @inheritdoc
     */
    public function getDescription(): DBHTMLText
    {
        return $this->renderWith($this->getViewerTemplates('_Description'));
    }

    /**
     * @inheritdoc
     */
    public function getPrice(): DBPrice
    {
        return $this->getField('Price');
    }

    /**
     * @inheritDoc
     * @codeCoverageIgnore
     */
    public function getCMSFields()
    {
        $this->beforeUpdateCMSFields(function (FieldList $fields) {
            $fields->insertAfter('MenuTitle', $this->obj('ShortDescription')->scaffoldFormField());
            $fields->insertAfter('ShortDescription', $this->obj('Price')->scaffoldFormField());
        });

        return parent::getCMSFields();
    }
}
