<?php
declare(strict_types=1);

namespace SwipeStripe\Common\Product;

use SilverStripe\Forms\FieldList;
use SilverStripe\Security\Permission;
use SwipeStripe\Order\PurchasableInterface;
use SwipeStripe\Price\DBPrice;
use SwipeStripe\ShopPermissions;

/**
 * Class SimpleProduct
 * @package SwipeStripe\Common\Product
 * @property string $Description
 * @property DBPrice $Price
 */
class SimpleProduct extends \Page implements PurchasableInterface
{
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

    /**
     * @inheritDoc
     */
    public function canView($member = null)
    {
        return Permission::check(ShopPermissions::VIEW_PRODUCTS) || parent::canView($member);
    }

    /**
     * @inheritDoc
     */
    public function canPublish($member = null)
    {
        return Permission::check(ShopPermissions::EDIT_PRODUCTS) || parent::canPublish($member);
    }

    /**
     * @inheritDoc
     */
    public function canDelete($member = null)
    {
        return Permission::check(ShopPermissions::DELETE_PRODUCTS) || parent::canDelete($member);
    }

    /**
     * @inheritDoc
     */
    public function canCreate($member = null, $context = [])
    {
        return Permission::check(ShopPermissions::CREATE_PRODUCTS) || parent::canCreate($member, $context);
    }

    /**
     * @inheritDoc
     */
    public function canEdit($member = null)
    {
        return Permission::check(ShopPermissions::EDIT_PRODUCTS) || parent::canEdit($member);
    }
}
