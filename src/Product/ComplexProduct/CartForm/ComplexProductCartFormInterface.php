<?php
declare(strict_types=1);

namespace SwipeStripe\Common\Product\ComplexProduct\CartForm;

use SilverStripe\Forms\Form;
use SwipeStripe\Common\Product\ComplexProduct\ComplexProduct;
use SwipeStripe\Order\Order;

/**
 * Class ComplexProductCartForm
 * @package SwipeStripe\Common\Product\ComplexProduct\CartForm
 * @mixin Form
 */
interface ComplexProductCartFormInterface
{
    /**
     * @return Order
     */
    public function getCart(): Order;

    /**
     * @param Order $cart
     * @return $this
     */
    public function setCart(Order $cart): self;

    /**
     * @return ComplexProduct
     */
    public function getProduct(): ComplexProduct;

    /**
     * @param ComplexProduct $product
     * @return $this
     */
    public function setProduct(ComplexProduct $product): self;
}
