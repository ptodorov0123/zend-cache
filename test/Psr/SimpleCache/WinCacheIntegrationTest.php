<?php
/**
 * @see       https://github.com/zendframework/zend-cache for the canonical source repository
 * @copyright Copyright (c) 2018 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-cache/blob/master/LICENSE.md New BSD License
 */

namespace ZendTest\Cache\Psr\SimpleCache;

use Cache\IntegrationTests\SimpleCacheTest;
use Zend\Cache\Psr\SimpleCache\SimpleCacheDecorator;
use Zend\Cache\Storage\Adapter\WinCache;
use Zend\Cache\StorageFactory;
use Zend\Cache\Exception;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;

/**
 * @requires extension wincache
 */
class WinCacheIntegrationTest extends SimpleCacheTest
{
    /**
     * Backup default timezone
     * @var string
     */
    private $tz;

    /**
     * @var WinCache
     */
    private $storage;

    protected function setUp()
    {
        if (! getenv('TESTS_ZEND_CACHE_WINCACHE_ENABLED')) {
            $this->markTestSkipped('Enable TESTS_ZEND_CACHE_WINCACHE_ENABLED to run this test');
        }

        // set non-UTC timezone
        $this->tz = date_default_timezone_get();
        date_default_timezone_set('America/Vancouver');

        parent::setUp();
    }

    protected function tearDown()
    {
        date_default_timezone_set($this->tz);

        if ($this->storage) {
            $this->storage->flush();
        }

        parent::tearDown();
    }

    public function createSimpleCache()
    {
        try {
            $storage = StorageFactory::adapterFactory('wincache');
            return new SimpleCacheDecorator($storage);
        } catch (Exception\ExtensionNotLoadedException $e) {
            $this->markTestSkipped($e->getMessage());
        } catch (ServiceNotCreatedException $e) {
            if ($e->getPrevious() instanceof Exception\ExtensionNotLoadedException) {
                $this->markTestSkipped($e->getMessage());
            }
            throw $e;
        }
    }
}
