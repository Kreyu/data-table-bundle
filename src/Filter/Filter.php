<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Filter;

use Kreyu\Bundle\DataTableBundle\DataTableInterface;
use Kreyu\Bundle\DataTableBundle\DataTableView;
use Kreyu\Bundle\DataTableBundle\Exception\BadMethodCallException;
use Kreyu\Bundle\DataTableBundle\Query\ProxyQueryInterface;

class Filter implements FilterInterface
{
    private ?DataTableInterface $dataTable = null;

    public function __construct(
        private readonly FilterConfigInterface $config,
    ) {
    }

    public function getName(): string
    {
        return $this->config->getName();
    }

    public function getConfig(): FilterConfigInterface
    {
        return $this->config;
    }

    public function getDataTable(): DataTableInterface
    {
        if (null === $this->dataTable) {
            throw new BadMethodCallException('Filter is not attached to any data table.');
        }

        return $this->dataTable;
    }

    public function setDataTable(DataTableInterface $dataTable): static
    {
        $this->dataTable = $dataTable;

        return $this;
    }

    public function getFormName(): string
    {
        return str_replace('.', '__', $this->getName());
    }

    public function getFormOptions(): array
    {
        return [
            'value_form_type' => $this->config->getValueFormType(),
            'value_form_options' => $this->config->getValueFormOptions(),
            'operator_form_type' => $this->config->getOperatorFormType(),
            'operator_form_options' => $this->config->getOperatorFormOptions(),
            'default_operator' => $this->config->getDefaultOperator(),
            'supported_operators' => $this->config->getSupportedOperators(),
            'operator_selectable' => $this->config->isOperatorSelectable(),
        ];
    }

    public function getQueryPath(): string
    {
        return $this->config->getOption('query_path', $this->getName());
    }

    public function apply(ProxyQueryInterface $query = null, FilterData $data = null): void
    {
        $query ??= $this->getDataTable()->getQuery();

        if (null === $query) {
            $error = 'Unable to apply filter without a query.';
            $error .= ' Either ensure the related data table has a query or pass one explicitly.';

            throw new BadMethodCallException($error);
        }

        $data ??= $this->getDataTable()->getFiltrationData()->getFilterData($this);

        if (null === $data) {
            $error = 'Unable to apply filter without filter data.';
            $error .= ' Either ensure the related data table has filter data or pass one explicitly.';

            throw new BadMethodCallException($error);
        }

        $this->config->getType()->apply($query, $data, $this, $this->config->getOptions());
    }

    public function createView(FilterData $data, DataTableView $parent): FilterView
    {
        $view = $this->config->getType()->createView($this, $data, $parent);

        $this->config->getType()->buildView($view, $this, $data, $this->config->getOptions());

        return $view;
    }
}
