<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Filter;

use Kreyu\Bundle\DataTableBundle\DataTableView;
use Kreyu\Bundle\DataTableBundle\Filter\Type\ResolvedFilterTypeInterface;
use Kreyu\Bundle\DataTableBundle\Query\ProxyQueryInterface;

class Filter implements FilterInterface
{
    private ?FilterData $data = null;

    public function __construct(
        private string $name,
        private ResolvedFilterTypeInterface $type,
        private array $options = [],
    ) {
    }

    public function getData(): ?FilterData
    {
        return $this->data;
    }

    public function setData(?FilterData $data): void
    {
        $this->data = $data;
    }

    public function apply(ProxyQueryInterface $query, FilterData $data): void
    {
        $this->type->apply($query, $data, $this, $this->options);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getFormName(): string
    {
        return str_replace('.', '__', $this->getName());
    }

    public function getFormOptions(): array
    {
        return [
            'label' => $this->getOption('label'),
            'translation_domain' => $this->getOption('translation_domain'),
            'field_type' => $this->getOption('field_type'),
            'field_options' => $this->getOption('field_options'),
            'operator_type' => $this->getOption('operator_type'),
            'operator_options' => $this->getOption('operator_options'),
            'active_filter_formatter' => $this->getOption('active_filter_formatter'),
        ];
    }

    public function getQueryPath(): string
    {
        return $this->options['query_path'] ?? $this->name;
    }

    public function getType(): ResolvedFilterTypeInterface
    {
        return $this->type;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function getOption(string $name): mixed
    {
        return $this->options[$name];
    }

    public function createView(DataTableView $parent = null): FilterView
    {
        $view = $this->type->createView($this, $parent);

        $this->type->buildView($view, $this, $this->options);

        return $view;
    }
}
