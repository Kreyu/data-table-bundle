<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column\Type;

use Kreyu\Bundle\DataTableBundle\Column\View\ColumnViewInterface;
use Symfony\Component\Form\Util\StringUtil;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatableMessage;

abstract class AbstractType implements ColumnTypeInterface
{
    public function buildHeaderView(ColumnViewInterface $view): void
    {
    }

    public function buildValueView(ColumnViewInterface $view, mixed $value): void
    {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'label' => null,
                'label_translation_parameters' => [],
                'translation_domain' => 'KreyuDataTable',
                'property_path' => null,
                'sort_field' => false,
                'block_name' => 'data_table_'.$this->getBlockPrefix(),
                'block_prefix' => $this->getBlockPrefix(),
                'value' => null,
            ])
            ->setAllowedTypes('label', ['null', 'string', TranslatableMessage::class])
            ->setAllowedTypes('label_translation_parameters', ['array', 'callable'])
            ->setAllowedTypes('translation_domain', ['bool', 'string'])
            ->setAllowedTypes('property_path', ['null', 'bool', 'string'])
            ->setAllowedTypes('sort_field', ['bool', 'string'])
            ->setAllowedTypes('block_name', ['null', 'string'])
            ->setAllowedTypes('block_prefix', ['null', 'string']);
    }

    public function getBlockPrefix(): string
    {
        return StringUtil::fqcnToBlockPrefix(static::class) ?: '';
    }
}
