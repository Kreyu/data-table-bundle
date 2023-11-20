<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Action;

use Kreyu\Bundle\DataTableBundle\Action\Type\ResolvedActionTypeInterface;

interface ActionConfigBuilderInterface extends ActionConfigInterface
{
    /**
     * @deprecated since 0.14.0, provide the name using the factory {@see ActionFactoryInterface} "named" methods instead
     */
    public function setName(string $name): static;

    public function setType(ResolvedActionTypeInterface $type): static;

    /**
     * @deprecated since 0.14.0, modifying the options dynamically will be removed as it creates unexpected behaviors
     */
    public function setOptions(array $options): static;

    /**
     * @deprecated since 0.14.0, modifying the options dynamically will be removed as it creates unexpected behaviors
     */
    public function setOption(string $name, mixed $value): static;

    public function setAttributes(array $attributes): static;

    public function setAttribute(string $name, mixed $value): static;

    public function setContext(ActionContext $context): static;

    public function setConfirmable(bool $confirmable): static;

    public function setActionFactory(ActionFactoryInterface $actionFactory): static;

    public function getActionConfig(): ActionConfigInterface;
}
