<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Type;

use Kreyu\Bundle\DataTableBundle\Column\Mapper\ColumnMapperInterface;
use Kreyu\Bundle\DataTableBundle\Filter\Mapper\FilterMapperInterface;
use Symfony\Component\Form\Util\StringUtil;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractType implements DataTableTypeInterface
{
    public function configureOptions(OptionsResolver $resolver): void
    {
    }

    public function configureColumns(ColumnMapperInterface $columns, array $options): void
    {
    }

    public function configureFilters(FilterMapperInterface $filters, array $options): void
    {
    }

    public function getName(): ?string
    {
        return StringUtil::fqcnToBlockPrefix(static::class);
    }
}
