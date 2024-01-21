<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Filter;

use Kreyu\Bundle\DataTableBundle\DataTableInterface;
use Kreyu\Bundle\DataTableBundle\DataTableView;
use Kreyu\Bundle\DataTableBundle\Exception\BadMethodCallException;
use Kreyu\Bundle\DataTableBundle\Filter\Event\FilterEvent;
use Kreyu\Bundle\DataTableBundle\Filter\Event\FilterEvents;
use Kreyu\Bundle\DataTableBundle\Filter\Event\PostHandleEvent;
use Kreyu\Bundle\DataTableBundle\Filter\Event\PreHandleEvent;
use Kreyu\Bundle\DataTableBundle\Query\ProxyQueryInterface;

class Filter implements FilterInterface
{
    private DataTableInterface $dataTable;

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
        if (!isset($this->dataTable)) {
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
            'form_type' => $this->config->getFormType(),
            'form_options' => $this->config->getFormOptions() + ['required' => false],
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

    public function handle(ProxyQueryInterface $query, FilterData $data): void
    {
        $this->dispatch(FilterEvents::PRE_HANDLE, $event = new PreHandleEvent($query, $data, $this));

        $this->config->getHandler()->handle($query, $data = $event->getData(), $this);

        $this->dispatch(FilterEvents::POST_HANDLE, new PostHandleEvent($query, $data, $this));
    }

    public function createView(FilterData $data, DataTableView $parent): FilterView
    {
        $view = $this->config->getType()->createView($this, $data, $parent);

        $this->config->getType()->buildView($view, $this, $data, $this->config->getOptions());

        return $view;
    }

    private function dispatch(string $eventName, FilterEvent $event): void
    {
        $dispatcher = $this->config->getEventDispatcher();

        if ($dispatcher->hasListeners($eventName)) {
            $dispatcher->dispatch($event, $eventName);
        }
    }

    /**
     * @deprecated since 0.15, use {@see Filter::handle()} instead
     */
    public function apply(ProxyQueryInterface $query = null, FilterData $data = null): void
    {
        $query ??= $this->getDataTable()->getQuery();

        $data ??= $this->getDataTable()->getFiltrationData()->getFilterData($this);

        if (null === $data) {
            $error = 'Unable to apply filter without filter data.';
            $error .= ' Either ensure the related data table has filter data or pass one explicitly.';

            throw new BadMethodCallException($error);
        }

        $type = $this->config->getType();

        if (method_exists($type, 'apply')) {
            $type->apply($query, $data, $this, $this->config->getOptions());

            return;
        }

        $this->handle($query, $data);
    }
}
