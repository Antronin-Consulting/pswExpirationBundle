<?php

/**
 * File: tests\PswExpirationBundleTest.php
 * Author: Peter Nagy <peter@antronin.consulting>
 * -----.
 */

declare(strict_types=1);

namespace AntroninConsulting\PswExpirationBundle\Tests;

use AntroninConsulting\PswExpirationBundle\Tests\Fixture\Kernel;
use PHPUnit\Framework\TestCase;
use AntroninConsulting\PswExpirationBundle\Config\Unit;

class PswExpirationBundleTest extends TestCase
{
    public function testLoadExtension(): void
    {
        $_ENV['PSW_LIFETIME'] = '90';
        $_ENV['PSW_WARNING'] = '14';
        $_ENV['PSW_UNIT'] = 'days';
        $kernel = new Kernel('test', true);
        $kernel->boot();
        $container = $kernel->getContainer();

        self::assertSame(90, $container->getParameter('psw_expiration.lifetime'));
        self::assertSame(14, $container->getParameter('psw_expiration.warning_threshold'));
        self::assertSame(Unit::DAYS, $container->getParameter('psw_expiration.unit'));
    }

    public function testLoadExtensionWithEnvVars(): void
    {
        $_ENV['PSW_LIFETIME'] = '60';
        $_ENV['PSW_WARNING'] = '10';
        $_ENV['PSW_UNIT'] = 'weeks';

        $kernel = new Kernel('test', true);
        $kernel->boot();
        $container = $kernel->getContainer();

        self::assertSame(60, $container->getParameter('psw_expiration.lifetime'));
        self::assertSame(10, $container->getParameter('psw_expiration.warning_threshold'));
        self::assertSame(Unit::WEEKS, $container->getParameter('psw_expiration.unit'));
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($_ENV['PSW_LIFETIME'], $_ENV['PSW_WARNING'], $_ENV['PSW_UNIT']);

        // Clean up the cache created by the kernel
        $fs = new \Symfony\Component\Filesystem\Filesystem();
        $fs->remove(__DIR__ . '/../var');
    }
}
