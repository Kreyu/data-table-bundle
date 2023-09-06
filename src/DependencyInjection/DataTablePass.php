<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\DependencyInjection;

use Kreyu\Bundle\DataTableBundle\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Argument\IteratorArgument;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Compiler\PriorityTaggedServiceTrait;
use Symfony\Component\DependencyInjection\Compiler\ServiceLocatorTagPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class DataTablePass implements CompilerPassInterface
{
    use PriorityTaggedServiceTrait;

    private array $extensions = [
        'kreyu_data_table.extension',
        'kreyu_data_table.column.extension',
        'kreyu_data_table.filter.extension',
        'kreyu_data_table.exporter.extension',
        'kreyu_data_table.action.extension',
    ];

    public function process(ContainerBuilder $container): void
    {
        foreach ($this->extensions as $extensionId) {
            $this->processExtension($container, $extensionId);
        }
    }

    private function processExtension(ContainerBuilder $container, string $extensionId): void
    {
        if (!$container->hasDefinition($extensionId)) {
            return;
        }

        $definition = $container->getDefinition($extensionId);
        $attributes = $definition->getTag($extensionId)[0];

        $definition->replaceArgument(0, $this->processTypes($container, $attributes['type']));
        $definition->replaceArgument(1, $this->processTypeExtensions($container, $attributes['type_extension']));
    }

    private function processTypes(ContainerBuilder $container, string $tagName): Reference
    {
        $servicesMap = [];

        foreach ($container->findTaggedServiceIds($tagName, true) as $serviceId => $reference) {
            $serviceDefinition = $container->getDefinition($serviceId);
            $servicesMap[$serviceDefinition->getClass()] = new Reference($serviceId);
        }

        return ServiceLocatorTagPass::register($container, $servicesMap);
    }

    private function processTypeExtensions(ContainerBuilder $container, string $tagName): array
    {
        $typeExtensions = [];

        foreach ($this->findAndSortTaggedServices($tagName, $container) as $reference) {
            $serviceId = (string) $reference;
            $serviceDefinition = $container->getDefinition($serviceId);

            $tag = $serviceDefinition->getTag($tagName);
            $typeExtensionClass = $container->getParameterBag()->resolveValue($serviceDefinition->getClass());

            if (isset($tag[0]['extended_type'])) {
                $typeExtensions[$tag[0]['extended_type']][] = new Reference($serviceId);
            } else {
                $extendsTypes = false;

                foreach ($typeExtensionClass::getExtendedTypes() as $extendedType) {
                    $typeExtensions[$extendedType][] = new Reference($serviceId);
                    $extendsTypes = true;
                }

                if (!$extendsTypes) {
                    throw new InvalidArgumentException(sprintf('The getExtendedTypes() method for service "%s" does not return any extended types.', $serviceId));
                }
            }
        }

        foreach ($typeExtensions as $extendedType => $extensions) {
            $typeExtensions[$extendedType] = new IteratorArgument($extensions);
        }

        return $typeExtensions;
    }
}
