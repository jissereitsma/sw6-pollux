<?php

namespace Yireo\SymfonyTests\Test\Integration;

use Shopware\Core\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class TestKernel extends Kernel
{
    protected string $testConfigFile = '';

    /**
     * @param string $testConfigFile
     */
    public function setTestConfigFile(string $testConfigFile): void
    {
        $this->testConfigFile = $testConfigFile;
    }

    protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader): void
    {
        parent::configureContainer($container, $loader);
        if (file_exists($this->testConfigFile)) {
            $loader->load($this->testConfigFile);
        }
    }
}
