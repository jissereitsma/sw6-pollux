<?php declare(strict_types=1);

namespace Yireo\SymfonyTests\Test\Integration;

use Yireo\SymfonyTests\Service\DummyService;

class ServicesBasicTest extends AbstractTestCase
{
    const TEST_CONFIG_FILE = __DIR__ . '/Fixture/services_basic.xml';

    public function testIfBasicServiceDefinitionWorks()
    {
        $dummyService = $this->getService(DummyService::class);
        $this->assertInstanceOf(DummyService::class, $dummyService);
        $this->assertEquals('dummy', $dummyService->getDummy());
    }
}
