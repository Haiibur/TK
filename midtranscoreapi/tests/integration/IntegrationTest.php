<?php

namespace Midtrans\integration;

use Midtrans\Config;

abstract class IntegrationTest extends \PHPUnit_Framework_TestCase
{
    public static function setUpBeforeClass()
    {
        Config::$serverKey = getenv('SB-Mid-server-3XqDS86A6xIG0SDj7UzWa_BY');
        Config::$clientKey = getenv('SB-Mid-client-S0L-1w3YEvX_VaJD');
        Config::$isProduction = false;
    }

    public function tearDown()
    {
        // One second interval to avoid throttle
        sleep(1);
    }
}
