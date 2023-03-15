<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Action\Type;

use Kreyu\Bundle\DataTableBundle\Action\ActionInterface;
use Kreyu\Bundle\DataTableBundle\Action\ActionView;
use Kreyu\Bundle\DataTableBundle\Action\Extension\ActionTypeExtensionInterface;
use Kreyu\Bundle\DataTableBundle\DataTableView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ResolvedActionType implements ResolvedActionTypeInterface
{
    private OptionsResolver $optionsResolver;

    /**
     * @param array<ActionTypeExtensionInterface> $typeExtensions
     */
    public function __construct(
        private ActionTypeInterface $innerType,
        private array $typeExtensions = [],
        private ?ResolvedActionTypeInterface $parent = null,
    ) {
    }

    public function getBlockPrefix(): string
    {
        return $this->innerType->getBlockPrefix();
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

    public function createView(ActionInterface $action, DataTableView $parent = null): ActionView
    {
        return new ActionView($parent);
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
