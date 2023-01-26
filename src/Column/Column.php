<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column;

use Kreyu\Bundle\DataTableBundle\Column\Type\ResolvedColumnTypeInterface;
use Kreyu\Bundle\DataTableBundle\DataTableView;

class Column implements ColumnInterface
{
    private mixed $data = null;

    public function __construct(
        private string $name,
        private ResolvedColumnTypeInterface $type,
        private array $options = [],
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): ResolvedColumnTypeInterface
    {
        return $this->type;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function getData(): mixed
    {
        return $this->data;
    }

    public function setData(mixed $data): void
    {
        $this->data = $data;
    }

    public function createView(DataTableView $parent = null): ColumnView
    {
        $view = $this->type->createView($this, $parent);

        $this->type->buildView($view, $this, $this->options);

        return $view;
    }
}
