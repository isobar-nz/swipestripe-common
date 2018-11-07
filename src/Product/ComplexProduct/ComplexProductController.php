<?php
declare(strict_types=1);

namespace SwipeStripe\Common\Product\ComplexProduct;

use SilverStripe\Core\Injector\Injector;
use SwipeStripe\Common\Product\ComplexProduct\CartForm\ComplexProductCartFormInterface;
use SwipeStripe\HasActiveCart;

/**
 * Class ComplexProductController
 * @package SwipeStripe\Common\Product\ComplexProduct
 * @property ComplexProduct $dataRecord
 * @method ComplexProduct data()
 */
class ComplexProductController extends \PageController
{
    use HasActiveCart;

    /**
     * @var array
     */
    private static $allowed_actions = [
        'ComplexProductCartForm',
    ];

    /**
     * @return ComplexProductCartFormInterface
     */
    public function ComplexProductCartForm(): ComplexProductCartFormInterface
    {
        /** @var ComplexProductCartFormInterface $form */
        $form = Injector::inst()->create(ComplexProductCartFormInterface::class, $this->dataRecord, $this,
            __FUNCTION__);
        $form->setCart($this->ActiveCart);

        return $form;
    }
}
