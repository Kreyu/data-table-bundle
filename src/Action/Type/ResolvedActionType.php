<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Action\Type;

use Kreyu\Bundle\DataTableBundle\Action\ActionBuilder;
use Kreyu\Bundle\DataTableBundle\Action\ActionBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Action\ActionFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Action\ActionInterface;
use Kreyu\Bundle\DataTableBundle\Action\ActionView;
use Kreyu\Bundle\DataTableBundle\Action\Extension\ActionTypeExtensionInterface;
use Kreyu\Bundle\DataTableBundle\Column\ColumnValueView;
use Kreyu\Bundle\DataTableBundle\DataTableView;
use Symfony\Component\OptionsResolver\Exception\ExceptionInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ResolvedActionType implements ResolvedActionTypeInterface
{
    private OptionsResolver $optionsResolver;

    /**
     * @param array<ActionTypeExtensionInterface> $typeExtensions
     */
    public function __construct(
        private readonly ActionTypeInterface $innerType,
        private readonly array $typeExtensions = [],
        private readonly ?ResolvedActionTypeInterface $parent = null,
    ) {
    }

    public function getBlockPrefix(): string
    {
        return $this->innerType->getBlockPrefix();
    }

    public function getBlockPrefixHierarchy(): array
    {
        $blockPrefixes = [
            $this->getBlockPrefix(),
        ];

        $type = $this;

        while (null !== $type->getParent()) {
            $blockPrefixes[] = ($type = $type->getParent())->getBlockPrefix();
        }

        return array_unique($blockPrefixes);
    }

    public function getParent(): ?ResolvedActionTypeInterface
    {
        return $this->parent;
    }

    public function getInnerType(): ActionTypeInterface
    {
        return $this->innerType;
    }

    public function getTypeExtensions(): array
    {
        return $this->typeExtensions;
    }

    /**
     * @throws ExceptionInterface
     */
    public function createBuilder(ActionFactoryInterface $factory, string $name, array $options): ActionBuilderInterface
    {
        try {
            $options = $this->getOptionsResolver()->resolve($options);
        } catch (ExceptionInterface $exception) {
            throw new $exception(sprintf('An error has occurred resolving the options of the action "%s": ', get_debug_type($this->getInnerType())).$exception->getMessage(), $exception->getCode(), $exception);
        }

        $builder = new ActionBuilder($name, $this, $options);
        $builder->setActionFactory($factory);

        return $builder;
    }

    public function createView(ActionInterface $action, DataTableView|ColumnValueView $parent): ActionView
    {
        return new ActionView($parent);
    }

    public function buildAction(ActionBuilderInterface $builder, array $options): void
    {
        $this->parent?->buildAction($builder, $options);

        $this->innerType->buildAction($builder, $options);

        foreach ($this->typeExtensions as $extension) {
            $extension->buildAction($builder, $options);
        }
    }

    public function buildView(ActionView $view, ActionInterface $action, array $options): void
    {
        $this->parent?->buildView($view, $action, $options);

        $this->innerType->buildView($view, $action, $options);

        foreach ($this->typeExtensions as $extension) {
            $extension->buildView($view, $action, $options);
        }
    }

    public function getOptionsResolver(): OptionsResolver
    {
        if (!isset($this->optionsResolver)) {
            if (null !== $this->parent) {
                $this->optionsResolver = clone $this->parent->getOptionsResolver();
            } else {
                $this->optionsResolver = new OptionsResolver();
            }

            $this->innerType->configureOptions($this->optionsResolver);

            foreach ($this->typeExtensions as $extension) {
                $extension->configureOptions($this->optionsResolver);
            }
        }

        return $this->optionsResolver;
    }
}
