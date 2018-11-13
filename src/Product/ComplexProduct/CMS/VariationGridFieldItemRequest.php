<?php
declare(strict_types=1);

namespace SwipeStripe\Common\Product\ComplexProduct\CMS;

use SilverStripe\Forms\GridField\GridFieldDetailForm_ItemRequest;
use SilverStripe\ORM\ValidationResult;
use SwipeStripe\Common\Product\ComplexProduct\ComplexProductVariation;

/**
 * Class VariationGridFieldItemRequest
 * @package SwipeStripe\Common\Product\ComplexProduct\CMS
 */
class VariationGridFieldItemRequest extends GridFieldDetailForm_ItemRequest
{
    /**
     * @var array
     */
    private static $allowed_actions = [
        'ItemEditForm',
    ];

    /**
     * @inheritDoc
     */
    public function ItemEditForm()
    {
        $form = parent::ItemEditForm();
        /** @var ComplexProductVariation $record */
        $record = $this->getRecord();

        if (!$record->IsComplete()->getValue()) {
            $form->setMessage(_t(self::class . '.VARIATION_INCOMPLETE', 'Note: this variation does not ' .
                'have an option selected for every attribute, and so cannot yet be purchased.'),
                ValidationResult::TYPE_WARNING);
        }

        return $form;
    }
}
