<?php
declare(strict_types=1);

namespace SwipeStripe\Common\Product;

use SilverStripe\Admin\ModelAdmin;
use SwipeStripe\Common\Product\ComplexProduct\ComplexProduct;
use SwipeStripe\ShopPermissions;

/**
 * Class ProductAdmin
 * @package SwipeStripe\Common\Product
 */
class ProductAdmin extends ModelAdmin
{
    /**
     * @var string
     */
    private static $url_segment = 'swipestripe/products';

    /**
     * @var string
     */
    private static $menu_title = 'Products';

    /**
     * @var array
     */
    private static $required_permission_codes = [
        ShopPermissions::VIEW_PRODUCTS,
    ];

    /**
     * @var array
     */
    private static $managed_models = [
        SimpleProduct::class,
        ComplexProduct::class,
    ];
}
