<?php

namespace Tests;

use App\Entities\Environment;
use App\Entities\EnvironmentLevel;

class Environment_Test extends TestCase
{
    private $originalEnv;

    protected function setUp(): void
    {
        parent::setUp();

        $this->originalEnv = config('app.env');
        Environment::resetLevel();
    }

    protected function tearDown(): void
    {
        config()->set('app.env', $this->originalEnv);

        parent::tearDown();
    }

    public function testCanGetLevel()
    {
        config()->set('app.env', EnvironmentLevel::ENV_PRODUCTION);

        $environment = Environment::getLevel();

        $this->assertEquals(EnvironmentLevel::ENV_PRODUCTION, $environment->valueOf());
    }

    public function testCanOverrideLevel()
    {
        $level = new EnvironmentLevel(EnvironmentLevel::ENV_STAGING);
        Environment::overrideLevel($level);

        $environment = Environment::getLevel();

        $this->assertEquals(EnvironmentLevel::ENV_STAGING, $environment->valueOf());
    }

    public function testIsProduction_whenProduction()
    {
        config()->set('app.env', EnvironmentLevel::ENV_PRODUCTION);

        $this->assertTrue(Environment::isProduction());
    }

    public function testIsProduction_whenStaging()
    {
        config()->set('app.env', EnvironmentLevel::ENV_STAGING);

        $this->assertFalse(Environment::isProduction());
    }
}
