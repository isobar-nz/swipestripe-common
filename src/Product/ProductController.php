<?php
declare(strict_types=1);

namespace SwipeStripe\Common\Product;

use SilverStripe\Control\HTTPRequest;
use SilverStripe\Control\HTTPResponse;
use SwipeStripe\Pages\HasActiveCart;

/**
 * Class ProductController
 * @package SwipeStripe\Common\Product
 * @property Product $dataRecord
 * @method Product data()
 */
class ProductController extends \PageController
{
    use HasActiveCart;

    /**
     * @var array
     */
    private static $allowed_actions = [
        'AddMore',
    ];

    /**
     * @var array
     */
    private static $url_handlers = [
        'add-more' => 'AddMore',
    ];

    /**
     * @param HTTPRequest $request
     * @return HTTPResponse
     */
    public function AddMore(HTTPRequest $request): HTTPResponse
    {
        if ($qty = intval($request->getVar('qty'))) {
            $this->ActiveCart->addItem($this->dataRecord, $qty);
        }

        return $this->redirectBack();
    }
}
