<?php
declare(strict_types=1);

namespace SwipeStripe\Common\Product;

use SilverStripe\Security\Permission;
use SwipeStripe\ShopPermissions;

/**
 * Trait ProductCMSPermissions
 * @package SwipeStripe\Common\Product
 * @mixin \Page
 */
trait ProductCMSPermissions
{
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
