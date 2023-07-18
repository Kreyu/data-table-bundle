<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Action\Type;

use Kreyu\Bundle\DataTableBundle\Action\ActionBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Action\ActionFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Action\ActionInterface;
use Kreyu\Bundle\DataTableBundle\Action\ActionView;
use Kreyu\Bundle\DataTableBundle\Action\Extension\ActionTypeExtensionInterface;
use Kreyu\Bundle\DataTableBundle\Column\ColumnValueView;
use Kreyu\Bundle\DataTableBundle\DataTableView;
use Symfony\Component\OptionsResolver\OptionsResolver;

interface ResolvedActionTypeInterface
{
    public function getBlockPrefix(): string;

    public function getBlockPrefixHierarchy(): array;

    public function getParent(): ?ResolvedActionTypeInterface;

    public function getInnerType(): ActionTypeInterface;

    /**
     * @return array<ActionTypeExtensionInterface>
     */
    public function getTypeExtensions(): array;

    public function createBuilder(ActionFactoryInterface $factory, string $name, array $options = []): ActionBuilderInterface;

    public function createView(ActionInterface $action, DataTableView|ColumnValueView $parent): ActionView;

    public function buildAction(ActionBuilderInterface $builder, array $options): void;

    public function buildView(ActionView $view, ActionInterface $action, array $options): void;

    public function getOptionsResolver(): OptionsResolver;
}
