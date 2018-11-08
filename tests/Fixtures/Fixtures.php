<?php
declare(strict_types=1);

namespace SwipeStripe\Common\Tests\Fixtures;

/**
 * Class Fixtures
 * @package SwipeStripe\Common\Tests\Fixtures
 */
final class Fixtures
{
    const BASE_PATH = __DIR__;

    const COMPLEX_PRODUCTS = self::BASE_PATH . '/complex-products.yml';
    const SIMPLE_PRODUCTS = self::BASE_PATH . '/simple-products.yml';

    /**
     * Fixtures constructor.
     */
    private function __construct()
    {
    }
}
