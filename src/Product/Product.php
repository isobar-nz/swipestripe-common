<?php
declare(strict_types=1);

namespace SwipeStripe\Common\Product;

use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\FieldType\DBText;
use SwipeStripe\Order\PurchasableInterface;
use SwipeStripe\Price\DBPrice;

/**
 * Class Product
 * @package SwipeStripe\Common\Product
 * @property string $Description
 * @property DBPrice $Price
 */
class Product extends \Page implements PurchasableInterface
{
    /**
     * @var string
     */
    private static $table_name = 'SwipeStripe_Common_Product';

    /**
     * @var array
     */
    private static $db = [
        'Description' => DBText::class,
        'Price'       => DBPrice::class,
    ];

    /**
     * @inheritdoc
     */
    public function getDescription(): string
    {
        return $this->getField('Description') ?? '';
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

    /**
     * @inheritdoc
     */
    public function getOrderInlineCMSFields(): FieldList
    {
        return FieldList::create([
            TextField::create('Title')->setValue($this->Title),
            TextField::create('Description')->setValue($this->Description),
        ]);
    }
}
