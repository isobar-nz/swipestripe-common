<?php
declare(strict_types=1);

namespace SwipeStripe\Common\Checkout;

use SilverStripe\Core\Extension;
use SwipeStripe\Common\Product\ComplexProduct\ComplexProductVariation;
use SwipeStripe\Common\Product\SimpleProduct;
use SwipeStripe\Order\Checkout\CheckoutFormInterface;
use SwipeStripe\Order\Checkout\CheckoutFormValidator;
use SwipeStripe\Order\PurchasableInterface;

/**
 * Class CheckoutFormExtension
 * @package SwipeStripe\Common\Checkout
 * @property-read CheckoutFormValidator|CheckoutFormValidatorExtension $owner
 */
class CheckoutFormValidatorExtension extends Extension
{
    /**
     * @param CheckoutFormInterface $form
     */
    public function validate(CheckoutFormInterface $form): void
    {
        $outOfStockProducts = [];

        foreach ($form->getCart()->OrderItems() as $orderItem) {
            $purchasable = $orderItem->Purchasable();

            if ($purchasable instanceof SimpleProduct || $purchasable instanceof ComplexProductVariation) {
                $this->validateItemInStock($purchasable, $outOfStockProducts);
            }
        }

        if (count($outOfStockProducts) > 0) {
            $this->owner->getResult()->addError(_t(self::class . '.OUT_OF_STOCK',
                'Sorry, but the following product(s) in your cart are out of stock: {products}.', [
                    'products' => implode(', ', $outOfStockProducts),
                ]));
        }
    }

    /**
     * @param SimpleProduct|ComplexProductVariation $product
     * @param array $outOfStockProducts
     */
    protected function validateItemInStock(PurchasableInterface $product, array &$outOfStockProducts): void
    {
        if ($product->IsOutOfStock() ||
            ($product instanceof ComplexProductVariation && $product->Product()->IsOutOfStock())) {
            $outOfStockProducts[] = $product->Title;
        }
    }
}
