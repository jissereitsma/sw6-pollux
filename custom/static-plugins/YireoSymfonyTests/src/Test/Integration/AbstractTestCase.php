<?php declare(strict_types=1);

namespace Yireo\SymfonyTests\Test\Integration;

use Shopware\Core\Framework\Plugin\KernelPluginLoader\StaticKernelPluginLoader;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Yireo\SymfonyTests\YireoSymfonyTests;

abstract class AbstractTestCase extends KernelTestCase
{
    protected ?ContainerInterface $container = null;
    const TEST_CONFIG_FILE = '';

    protected function setUp(): void
    {
        self::bootKernel([
            'plugins' => [
                [
                    'path' => $_ENV['PROJECT_ROOT'] . '/custom/static-plugins/YireoSymfonyTests/src',
                    'baseClass' => YireoSymfonyTests::class,
                    'name' => YireoSymfonyTests::class,
                    'managedByComposer' => true,
                    'active' => true
                ]
            ]
        ]);

        $this->container = static::getContainer();

        parent::setUp();
    }

    protected static function createKernel(array $options = []): KernelInterface
    {
        static::$class ??= static::getKernelClass();

        if (isset($options['environment'])) {
            $env = $options['environment'];
        } elseif (isset($_ENV['APP_ENV'])) {
            $env = $_ENV['APP_ENV'];
        } elseif (isset($_SERVER['APP_ENV'])) {
            $env = $_SERVER['APP_ENV'];
        } else {
            $env = 'test';
        }

        if (isset($options['debug'])) {
            $debug = $options['debug'];
        } elseif (isset($_ENV['APP_DEBUG'])) {
            $debug = $_ENV['APP_DEBUG'];
        } elseif (isset($_SERVER['APP_DEBUG'])) {
            $debug = $_SERVER['APP_DEBUG'];
        } else {
            $debug = true;
        }

        $projectRoot = $_ENV['PROJECT_ROOT'];
        $classLoader = require $projectRoot . '/vendor/autoload.php';

        $staticPluginLoader = new StaticKernelPluginLoader(
            $classLoader,
            $projectRoot . '/custom/plugins',
            $options['plugins']
        );

        $kernel = new TestKernel(
            $env,
            (bool)$debug,
            $staticPluginLoader,
            'test',
        );

        if (self::TEST_CONFIG_FILE) {
            $kernel->setTestConfigFile(self::TEST_CONFIG_FILE);
        }

        return $kernel;
    }

    protected function getService(string $serviceClass)
    {
        try {
            return $this->container->get($serviceClass);
        } catch (ServiceNotFoundException $serviceNotFoundException) {
            $this->output('Service "' . $serviceClass . '" not found: ' . $serviceNotFoundException->getMessage());
            return null;
        }
    }
}
