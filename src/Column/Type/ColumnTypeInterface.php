<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column\Type;

use Kreyu\Bundle\DataTableBundle\Column\ColumnInterface;
use Kreyu\Bundle\DataTableBundle\Column\ColumnView;
use Symfony\Component\OptionsResolver\OptionsResolver;

interface ColumnTypeInterface
{
    public function buildView(ColumnView $view, ColumnInterface $column, array $options): void;

    public function configureOptions(OptionsResolver $resolver): void;

    public function getBlockPrefix(): string;

    /**
     * @return null|class-string<ColumnTypeInterface>
     */
    public function getParent(): ?string;
}
