<?php
declare(strict_types=1);

namespace SwipeStripe\Common\Product\ComplexProduct\CartForm;

use SilverStripe\Control\HTTPResponse;
use SilverStripe\Forms\FormRequestHandler;
use SilverStripe\ORM\ValidationException;
use SilverStripe\ORM\ValidationResult;
use SwipeStripe\Common\Product\ComplexProduct\ComplexProductVariation;
use SwipeStripe\HasActiveCart;

/**
 * Class ComplexProductCartFormRequestHandler
 * @package SwipeStripe\Common\Product\ComplexProduct\CartForm
 * @property ComplexProductCartForm $form
 */
class ComplexProductCartFormRequestHandler extends FormRequestHandler
{
    use HasActiveCart;

    /**
     * @inheritDoc
     */
    public function __construct(ComplexProductCartForm $form)
    {
        parent::__construct($form);
    }

    /**
     * @param array $data
     * @param ComplexProductCartForm $form
     * @return HTTPResponse
     * @throws \Exception
     */
    public function AddToCart(array $data, ComplexProductCartForm $form): HTTPResponse
    {
        $ids = [];
        foreach ($form->Fields()->dataFields() as $dataField) {
            if ($dataField instanceof ProductAttributeField) {
                $ids[] = intval($dataField->dataValue());
            }
        }

        $quantityField = $form->Fields()->dataFieldByName(ComplexProductCartForm::QUANTITY_FIELD);
        $variation = ComplexProductVariation::getVariationWithExactOptions($form->getProduct(), $ids);

        if ($variation === null) {
            throw ValidationException::create(ValidationResult::create()
                ->addError('Sorry, that combination of options is not currently available.'));
        }

        $this->ActiveCart->addItem($variation, $quantityField->dataValue());

        return $this->redirectBack();
    }
}
