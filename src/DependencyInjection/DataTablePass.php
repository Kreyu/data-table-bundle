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

    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition('kreyu_data_table.extension')) {
            return;
        }

        $definition = $container->getDefinition('kreyu_data_table.extension');
        $definition->replaceArgument(0, $this->processDataTableTypes($container));
        $definition->replaceArgument(1, $this->processDataTableTypeExtensions($container));
    }

    private function processDataTableTypes(ContainerBuilder $container): Reference
    {
        $servicesMap = [];

        foreach ($container->findTaggedServiceIds('kreyu_data_table.type', true) as $serviceId => $tag) {
            $serviceDefinition = $container->getDefinition($serviceId);
            $servicesMap[$serviceDefinition->getClass()] = new Reference($serviceId);
        }

        return ServiceLocatorTagPass::register($container, $servicesMap);
    }

    private function processDataTableTypeExtensions(ContainerBuilder $container): array
    {
        $typeExtensions = [];

        foreach ($this->findAndSortTaggedServices('kreyu_data_table.type_extension', $container) as $reference) {
            $serviceId = (string) $reference;
            $serviceDefinition = $container->getDefinition($serviceId);

            $tag = $serviceDefinition->getTag('kreyu_data_table.type_extension');
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
