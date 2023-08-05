<?php declare(strict_types=1);

namespace Yireo\SymfonyTests\Service;

class ExtendedDummyService extends DummyService
{
    public function getDummy(): string
    {
        return 'dummy_extended';
    }
}