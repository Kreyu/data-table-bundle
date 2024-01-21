<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Unit\Bridge\Doctrine\Orm\Fixtures;

use Doctrine\Common\EventManager;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMSetup;
use PHPUnit\Framework\TestCase;

class TestEntityManagerFactory
{
    public static function create(): EntityManagerInterface
    {
        if (!\extension_loaded('pdo_sqlite')) {
            TestCase::markTestSkipped('Extension pdo_sqlite is required.');
        }

        $config = ORMSetup::createAttributeMetadataConfiguration([], true);

        $connection = DriverManager::getConnection([
            'driver' => 'pdo_sqlite',
            'memory' => true,
        ], $config);

        return new EntityManager(
            $connection,
            $config,
            new EventManager(),
        );
    }
}
