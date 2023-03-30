<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Action;

use Kreyu\Bundle\DataTableBundle\Action\Type\ResolvedActionTypeInterface;
use Kreyu\Bundle\DataTableBundle\Column\ColumnValueView;
use Kreyu\Bundle\DataTableBundle\DataTableView;

class Action implements ActionInterface
{
    public function __construct(
        private string $name,
        private ResolvedActionTypeInterface $type,
        private array $options = [],
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): ResolvedActionTypeInterface
    {
        return $this->type;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function createView(DataTableView|ColumnValueView $parent): ActionView
    {
        $view = $this->type->createView($this, $parent);

        $this->type->buildView($view, $this, $this->options);

        return $view;
    }
}
