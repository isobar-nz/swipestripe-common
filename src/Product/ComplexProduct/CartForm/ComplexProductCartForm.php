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
class ComplexProductCartForm extends Form implements ComplexProductCartFormInterface
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
        ComplexProduct $product,
        RequestHandler $controller = null,
        string $name = self::DEFAULT_NAME
    ) {
        $this->setProduct($product);

        $fields = $this->buildFields();
        parent::__construct($controller, $name, $fields, $this->buildActions(),
            ComplexProductCartFormValidator::create($fields->dataFieldNames()));
    }

    /**
     * @return FieldList
     */
    protected function buildFields(): FieldList
    {
        $fields = FieldList::create();

        foreach ($this->getProduct()->ProductAttributes() as $attribute) {
            $fields->push(ProductAttributeField::create($attribute, "Attribute_{$attribute->ID}", $attribute->Title));
        }

        $fields->push(NumericField::create(static::QUANTITY_FIELD,
            _t(self::class . '.QUANTITY', 'Quantity'), 1));

        $this->extend('updateFields', $fields);
        return $fields;
    }

    /**
     * @return FieldList
     */
    protected function buildActions(): FieldList
    {
        $actions = FieldList::create(
            FormAction::create('AddToCart', _t(self::class . '.ADD_TO_CART', 'Add to cart'))
        );

        $this->extend('updateActions', $actions);
        return $actions;
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
    public function setCart(Order $cart): ComplexProductCartFormInterface
    {
        $this->cart = $cart;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setProduct(ComplexProduct $product): ComplexProductCartFormInterface
    {
        $this->product = $product;
        return $this;
    }

    /**
     * @inheritDoc
     */
    protected function buildRequestHandler()
    {
        return ComplexProductCartFormRequestHandler::create($this);
    }
}
