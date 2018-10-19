<?php
declare(strict_types=1);

namespace SwipeStripe\Common\Product\ComplexProduct;

use SwipeStripe\Common\Product\ComplexProduct\CartForm\ComplexProductCartForm;
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
     * @return ComplexProductCartForm
     */
    public function ComplexProductCartForm(): ComplexProductCartForm
    {
        return ComplexProductCartForm::create($this->ActiveCart, $this->dataRecord, $this, __FUNCTION__);
    }
}
