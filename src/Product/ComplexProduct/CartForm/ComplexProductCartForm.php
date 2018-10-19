<?php
declare(strict_types=1);

namespace SwipeStripe\Common\Product\ComplexProduct\CartForm;

use SilverStripe\Control\RequestHandler;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\Form;
use SilverStripe\Forms\FormAction;
use SilverStripe\Forms\NumericField;
use SwipeStripe\Common\Product\ComplexProduct\ComplexProduct;
use SwipeStripe\Order\Order;

/**
 * Class ComplexProductCartForm
 * @package SwipeStripe\Common\Product\ComplexProduct\CartForm
 */
class ComplexProductCartForm extends Form
{
    const QUANTITY_FIELD = 'Quantity';

    /**
     * @var Order
     */
    protected $cart;

    /**
     * @var ComplexProduct
     */
    protected $product;

    /**
     * @inheritDoc
     */
    public function __construct(
        Order $cart,
        ComplexProduct $product,
        RequestHandler $controller = null,
        string $name = self::DEFAULT_NAME
    ) {
        $this->cart = $cart;
        $this->product = $product;

        $fields = $this->buildFields();
        parent::__construct($controller, $name, $fields, $this->buildActions(),
            ComplexProductCartFormValidator::create($fields->dataFieldNames()));
    }

    /**
     * @return FieldList
     */
    protected function buildFields(): FieldList
    {
        $fields = [];

        foreach ($this->product->ProductAttributes() as $attribute) {
            $fields[] = ProductAttributeField::create($attribute, "Attribute_{$attribute->ID}", $attribute->Title);
        }

        $fields[] = NumericField::create(static::QUANTITY_FIELD, null, 1);

        return FieldList::create($fields);
    }

    /**
     * @return FieldList
     */
    protected function buildActions(): FieldList
    {
        return FieldList::create(
            FormAction::create('AddToCart', _t(self::class . '.ADD_TO_CART', 'Add to cart'))
        );
    }

    /**
     * @return Order
     */
    public function getCart(): Order
    {
        return $this->cart;
    }

    /**
     * @return ComplexProduct
     */
    public function getProduct(): ComplexProduct
    {
        return $this->product;
    }

    /**
     * @inheritDoc
     */
    protected function buildRequestHandler()
    {
        return ComplexProductCartFormRequestHandler::create($this);
    }
}
