<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Action;

use Kreyu\Bundle\DataTableBundle\Action\Type\ResolvedActionTypeInterface;
use Kreyu\Bundle\DataTableBundle\Column\ColumnValueView;
use Kreyu\Bundle\DataTableBundle\DataTableView;

interface ActionInterface
{
    public function getName(): string;

    public function getType(): ResolvedActionTypeInterface;

    public function getOptions(): array;

    public function createView(DataTableView|ColumnValueView $parent): ActionView;
}
