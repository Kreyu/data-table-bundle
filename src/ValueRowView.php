<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle;

use Kreyu\Bundle\DataTableBundle\Column\ColumnValueView;

class ValueRowView implements \ArrayAccess, \IteratorAggregate, \Countable
{
    public array $vars = [
        'attr' => [],
    ];

    /**
     * @var array<ColumnValueView>
     */
    public array $children = [];

    public ?ValueRowView $origin = null;

    public function __construct(
        public DataTableView $parent,
        public int $index,
        public mixed $data,
    ) {
    }

    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->children);
    }

    public function offsetExists(mixed $offset): bool
    {
        return array_key_exists($offset, $this->children);
    }

    public function offsetGet(mixed $offset): ColumnValueView
    {
        return $this->children[$offset];
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        throw new \BadMethodCallException('Not supported.');
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->children[$offset]);
    }

    public function count(): int
    {
        return count($this->children);
    }
}
