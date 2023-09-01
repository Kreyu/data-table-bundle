<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\DependencyInjection;

use Kreyu\Bundle\DataTableBundle\PersistenceContext;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Contracts\Cache\CacheInterface;

class DefaultConfigurationPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $extension = $container->getDefinition('kreyu_data_table.type.data_table');

        $defaults = $extension->getArgument('$defaults');

        foreach (PersistenceContext::cases() as $context) {
            $context = $context->value;

            if (!$defaults[$context]['persistence_enabled']) {
                continue;
            }

            $defaults[$context]['persistence_adapter'] ??= $this->getDefaultPersistenceAdapter($container, $context);
            $defaults[$context]['persistence_subject_provider'] ??= $this->getDefaultPersistenceSubjectProvider($container);
        }

        $extension->setArgument('$defaults', $defaults);
    }

    private function getDefaultPersistenceAdapter(ContainerBuilder $container, string $context): ?Reference
    {
        if ($container->has(CacheInterface::class)) {
            return new Reference("kreyu_data_table.$context.persistence.adapter.cache");
        }

        return null;
    }

    private function getDefaultPersistenceSubjectProvider(ContainerBuilder $container): ?Reference
    {
        if ($container->has(TokenStorageInterface::class)) {
            return new Reference('kreyu_data_table.persistence.subject_provider.token_storage');
        }

        return null;
    }
}
