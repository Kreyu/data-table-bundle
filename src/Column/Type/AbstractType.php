<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column\Type;

use Kreyu\Bundle\DataTableBundle\Column\ColumnInterface;
use Kreyu\Bundle\DataTableBundle\Column\ColumnView;
use Symfony\Component\Form\Util\StringUtil;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractType implements ColumnTypeInterface
{
    public function buildView(ColumnView $view, ColumnInterface $column, array $options): void
    {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
    }

    public function getBlockPrefix(): string
    {
        return StringUtil::fqcnToBlockPrefix(static::class) ?: '';
    }

    public function getParent(): ?string
    {
        return ColumnType::class;
    }
}
