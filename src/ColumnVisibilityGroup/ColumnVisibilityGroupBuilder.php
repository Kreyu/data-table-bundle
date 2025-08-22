<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\ColumnVisibilityGroup;

use Symfony\Component\OptionsResolver\OptionsResolver;

class ColumnVisibilityGroupBuilder implements ColumnVisibilityGroupBuilderInterface
{
    public function getColumnVisibilityGroup(string $name, array $options = []): ColumnVisibilityGroupInterface
    {
        $optionResolver = new OptionsResolver();
        $this->configureOptions($optionResolver);

        $resolvedOptions = $optionResolver->resolve($options);

        $columnVisibilityGroup = new ColumnVisibilityGroup();
        $columnVisibilityGroup->setName($name);
        $columnVisibilityGroup->setLabel($resolvedOptions['label'] ?? $name);
        $columnVisibilityGroup->setIsDefault($resolvedOptions['is_default']);

        return $columnVisibilityGroup;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'label' => null,
            'is_default' => false,
        ]);
    }
}
