<?php
declare(strict_types=1);

namespace SwipeStripe\Common\Tests;

/**
 * Class Fixtures
 * @package SwipeStripe\Common\Tests
 */
final class Fixtures
{
    const BASE_PATH = __DIR__ . '/fixtures';

    const COMPLEX_PRODUCTS = self::BASE_PATH . '/complex-products.yml';

    /**
     * Fixtures constructor.
     */
    private function __construct()
    {
    }
}
