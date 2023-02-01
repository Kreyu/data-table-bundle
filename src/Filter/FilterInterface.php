<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Filter;

use Kreyu\Bundle\DataTableBundle\DataTableView;
use Kreyu\Bundle\DataTableBundle\Filter\Type\ResolvedFilterTypeInterface;

interface FilterInterface
{
    public function getName(): string;

    public function getFormName(): string;

    public function getQueryPath(): string;

    public function getType(): ResolvedFilterTypeInterface;

    public function getOptions(): array;

    public function getOption(string $name): mixed;

    public function createView(DataTableView $parent = null): FilterView;
}
