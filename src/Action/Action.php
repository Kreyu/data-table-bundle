<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Action;

use Kreyu\Bundle\DataTableBundle\Action\Type\ResolvedActionTypeInterface;
use Kreyu\Bundle\DataTableBundle\DataTableView;

class Action implements ActionInterface
{
    private mixed $data = null;

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

    public function getData(): mixed
    {
        return $this->data;
    }

    public function setData(mixed $data): void
    {
        $this->data = $data;
    }

    public function createView(DataTableView $parent = null): ActionView
    {
        $view = $this->type->createView($this, $parent);

        $this->type->buildView($view, $this, $this->options);

        return $view;
    }
}
