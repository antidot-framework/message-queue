<?php

declare(strict_types=1);


namespace AntidotTest\Queue\Container\Config;

use Antidot\Queue\Container\Config\ConfigProvider;
use PHPUnit\Framework\TestCase;

class ConfigProviderTest extends TestCase
{
    public function testItShouldGetDefaultConfig(): void
    {
        $configProvider = new ConfigProvider();
        $this->assertEquals(ConfigProvider::DEFAULT_CONFIG, $configProvider->__invoke());
    }
}
