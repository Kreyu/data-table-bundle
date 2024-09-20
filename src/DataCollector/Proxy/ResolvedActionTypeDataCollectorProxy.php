<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\DataCollector\Proxy;

use Kreyu\Bundle\DataTableBundle\Action\ActionBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Action\ActionFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Action\ActionInterface;
use Kreyu\Bundle\DataTableBundle\Action\ActionView;
use Kreyu\Bundle\DataTableBundle\Action\Type\ActionTypeInterface;
use Kreyu\Bundle\DataTableBundle\Action\Type\ResolvedActionTypeInterface;
use Kreyu\Bundle\DataTableBundle\Column\ColumnValueView;
use Kreyu\Bundle\DataTableBundle\DataCollector\DataTableDataCollectorInterface;
use Kreyu\Bundle\DataTableBundle\DataTableView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ResolvedActionTypeDataCollectorProxy implements ResolvedActionTypeInterface
{
    public function __construct(
        private ResolvedActionTypeInterface $proxiedType,
        private DataTableDataCollectorInterface $dataCollector,
    ) {
    }

    public function getBlockPrefix(): string
    {
        return $this->proxiedType->getBlockPrefix();
    }

    public function getBlockPrefixHierarchy(): array
    {
        return $this->proxiedType->getBlockPrefixHierarchy();
    }

    public function getParent(): ?ResolvedActionTypeInterface
    {
        return $this->proxiedType->getParent();
    }

    public function getInnerType(): ActionTypeInterface
    {
        return $this->proxiedType->getInnerType();
    }

    public function getTypeExtensions(): array
    {
        return $this->proxiedType->getTypeExtensions();
    }

    public function createBuilder(ActionFactoryInterface $factory, string $name, array $options): ActionBuilderInterface
    {
        $builder = $this->proxiedType->createBuilder($factory, $name, $options);
        $builder->setAttribute('data_collector/passed_options', $options);
        $builder->setType($this);

        return $builder;
    }

    public function createView(ActionInterface $action, ColumnValueView|DataTableView $parent): ActionView
    {
        return $this->proxiedType->createView($action, $parent);
    }

    public function buildAction(ActionBuilderInterface $builder, array $options): void
    {
        $this->proxiedType->buildAction($builder, $options);
    }

    public function buildView(ActionView $view, ActionInterface $action, array $options): void
    {
        $this->proxiedType->buildView($view, $action, $options);
        $this->dataCollector->collectActionView($action, $view);
    }

    public function getOptionsResolver(): OptionsResolver
    {
        return $this->proxiedType->getOptionsResolver();
    }
}
