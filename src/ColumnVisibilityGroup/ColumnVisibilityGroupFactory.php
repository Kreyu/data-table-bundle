<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\ColumnVisibilityGroup;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class ColumnVisibilityGroupFactory implements ColumnVisibilityGroupFactoryInterface
{
    public function __construct(
        private TranslatorInterface $translator,
    ) {
    }

    public function create(string $name, array $options = []): ColumnVisibilityGroupInterface
    {
        $optionsResolver = new OptionsResolver();
        $this->configureOptions($optionsResolver);

        $resolvedOptions = $optionsResolver->resolve($options);

        return new ColumnVisibilityGroup(
            name: $name,
            label: $this->translator->trans($resolvedOptions['label'] ?? $name),
            isDefault: $resolvedOptions['is_default'],
        );
    }

    private function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'label' => null,
            'is_default' => false,
        ]);

        $resolver->setAllowedTypes('label', ['null', 'string']);
        $resolver->setAllowedTypes('is_default', 'bool');
    }
}
