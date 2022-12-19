<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column\Type;

use Kreyu\Bundle\DataTableBundle\Column\View\ColumnViewInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

interface ColumnTypeInterface
{
    public function buildHeaderView(ColumnViewInterface $view): void;

    public function buildValueView(ColumnViewInterface $view, mixed $value): void;

    public function configureOptions(OptionsResolver $resolver): void;

    public function getBlockPrefix(): string;
}
