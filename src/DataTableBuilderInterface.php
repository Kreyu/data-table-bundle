<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle;

use Kreyu\Bundle\DataTableBundle\Action\ActionBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Action\Type\ActionTypeInterface;
use Kreyu\Bundle\DataTableBundle\Exception\InvalidArgumentException;
use Kreyu\Bundle\DataTableBundle\Query\ProxyQueryInterface;

interface DataTableBuilderInterface extends DataTableConfigBuilderInterface
{
    public const BATCH_CHECKBOX_COLUMN_NAME = '__batch';

    /**
     * @return array<ActionBuilderInterface>
     */
    public function getActions(): array;

    /**
     * @throws InvalidArgumentException if action of given name does not exist
     */
    public function getAction(string $name): ActionBuilderInterface;

    /**
     * @param null|class-string<ActionTypeInterface> $type
     */
    public function addAction(ActionBuilderInterface|string $action, string $type = null, array $options = []): static;

    public function removeAction(string $name): static;

    /**
     * @return array<ActionBuilderInterface>
     */
    public function getBatchActions(): array;

    /**
     * @throws InvalidArgumentException if batch action of given name does not exist
     */
    public function getBatchAction(string $name): ActionBuilderInterface;

    /**
     * @param null|class-string<ActionTypeInterface> $type
     */
    public function addBatchAction(ActionBuilderInterface|string $action, string $type = null, array $options = []): static;

    public function removeBatchAction(string $name): static;

    public function isAutoAddingBatchCheckboxColumn(): bool;

    public function setAutoAddingBatchCheckboxColumn(bool $autoAddingBatchCheckboxColumn): static;

    public function getQuery(): ?ProxyQueryInterface;

    public function setQuery(?ProxyQueryInterface $query): static;

    public function getDataTable(): DataTableInterface;
}
