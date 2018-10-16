<?php
declare(strict_types=1);

namespace SwipeStripe\Common\Product\ComplexProduct\CartForm;

use SilverStripe\Forms\RequiredFields;

/**
 * Class ComplexProductCartFormValidator
 * @package SwipeStripe\Common\Product\ComplexProduct\CartForm
 */
class ComplexProductCartFormValidator extends RequiredFields
{
    /**
     * @inheritDoc
     */
    public function php($data)
    {
        parent::php($data);

        if ($this->getResult()->isValid() && intval($data[ComplexProductCartForm::QUANTITY_FIELD]) < 1) {
            $this->validationError(ComplexProductCartForm::QUANTITY_FIELD,
                _t(self::class . '.INVALID_QUANTITY', "You can't add less than one item to your cart!"));
        }

        return $this->getResult()->isValid();
    }

    /**
     * @inheritDoc
     */
    public function setForm($form)
    {
        if (!$form instanceof ComplexProductCartForm) {
            throw new \InvalidArgumentException();
        }

        return parent::setForm($form);
    }
}
