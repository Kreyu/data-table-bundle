<?php
declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Builder;

use Kreyu\Bundle\DataTableBundle\Action\ActionBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Action\Type\ActionTypeInterface;
use Kreyu\Bundle\DataTableBundle\Exception\InvalidArgumentException;

interface RowActionBuilderInterface
{
    /**
     * @throws InvalidArgumentException if row action of given name does not exist
     */
    public function getRowAction(string $name): ActionBuilderInterface;

    public function hasRowAction(string $name): bool;

    /**
     * @param class-string<ActionTypeInterface>|null $type
     */
    public function createRowAction(string $name, ?string $type = null, array $options = []): ActionBuilderInterface;

    /**
     * @param class-string<ActionTypeInterface>|null $type
     */
    public function addRowAction(ActionBuilderInterface|string $action, ?string $type = null, array $options = []): static;

    public function removeRowAction(string $name): static;
}
