<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Action;

use Kreyu\Bundle\DataTableBundle\Action\Type\ResolvedActionTypeInterface;

interface ActionConfigBuilderInterface extends ActionConfigInterface
{
    public function setName(string $name): static;

    public function setType(ResolvedActionTypeInterface $type): static;

    public function setOptions(array $options): static;

    public function setOption(string $name, mixed $value): static;

    public function setAttributes(array $attributes): static;

    public function setAttribute(string $name, mixed $value = null): static;

    public function setBatch(bool $batch): static;

    public function setConfirmable(bool $confirmable): static;

    public function getActionConfig(): ActionConfigInterface;
}
