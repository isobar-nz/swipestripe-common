<?php
declare(strict_types=1);

namespace SwipeStripe\Common\Product;

use SilverStripe\Forms\FieldList;
use SwipeStripe\Order\PurchasableInterface;
use SwipeStripe\Price\DBPrice;

/**
 * Class SimpleProduct
 * @package SwipeStripe\Common\Product
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
        'Description' => 'Text',
        'Price'       => 'Price',
    ];

    /**
     * @inheritdoc
     */
    public function getDescription(): string
    {
        return $this->getField('Description') ?? $this->obj('Content')->Summary() ?? '';
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
     */
    public function getCMSFields()
    {
        $this->beforeUpdateCMSFields(function (FieldList $fields) {
            $fields->insertAfter('MenuTitle', $this->obj('Description')->scaffoldFormField());
            $fields->insertAfter('Description', $this->obj('Price')->scaffoldFormField());
        });

        return parent::getCMSFields();
    }
}
