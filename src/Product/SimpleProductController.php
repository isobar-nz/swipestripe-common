<?php
declare(strict_types=1);

namespace SwipeStripe\Common\Product;

use SilverStripe\Control\HTTPRequest;
use SilverStripe\Control\HTTPResponse;
use SwipeStripe\HasActiveCart;

/**
 * Class SimpleProductController
 * @package SwipeStripe\Common\Product
 * @property SimpleProduct $dataRecord
 * @method SimpleProduct data()
 */
class SimpleProductController extends \PageController
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
        $qty = intval($request->getVar('qty'));
        if ($qty && $this->ActiveCart->IsMutable()) {
            $this->ActiveCart->addItem($this->dataRecord, $qty);
        }

        return $this->redirectBack();
    }
}
