<?php declare(strict_types=1);

namespace Yireo\SymfonyTests\Test\Integration;

use Yireo\SymfonyTests\Service\DummyService;

class ServicesAliasTest extends AbstractTestCase
{
    const TEST_CONFIG_FILE = __DIR__ . '/Fixture/services_alias.xml';

    public function testIfServiceAliasWorks()
    {
        $dummyServiceByClass = $this->getService(DummyService::class);
        $this->assertNotInstanceOf(DummyService::class, $dummyServiceByClass);

        $dummyService = $this->getService('dummy');
        $this->assertInstanceOf(DummyService::class, $dummyService);
        $this->assertEquals('dummy', $dummyService->getDummy());
    }
}
