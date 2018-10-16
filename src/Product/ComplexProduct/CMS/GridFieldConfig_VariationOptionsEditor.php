<?php
declare(strict_types=1);

namespace SwipeStripe\Common\Product\ComplexProduct\CMS;

use SilverStripe\Forms\GridField\GridFieldAddExistingAutocompleter;
use SilverStripe\Forms\GridField\GridFieldAddNewButton;
use SilverStripe\Forms\GridField\GridFieldConfig_RelationEditor;
use Symbiote\GridFieldExtensions\GridFieldAddExistingSearchButton;

/**
 * Class GridFieldConfig_VariationOptionEditor
 * @package SwipeStripe\Common\Product\ComplexProduct\CMS
 */
class GridFieldConfig_VariationOptionsEditor extends GridFieldConfig_RelationEditor
{
    /**
     * @inheritDoc
     */
    public function __construct(?int $itemsPerPage = null)
    {
        parent::__construct($itemsPerPage);

        $this->removeComponentsByType([
            GridFieldAddExistingAutocompleter::class,
            GridFieldAddNewButton::class,
        ]);
        $this->addComponent(new GridFieldAddExistingSearchButton());
    }
}
