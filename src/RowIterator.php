<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle;

/**
 * @template TValue
 *
 * @implements \Iterator<mixed, TValue>
 */
class RowIterator implements \Iterator
{
    /**
     * @var \Closure(): \Generator<mixed, TValue, mixed, mixed>
     */
    private \Closure $callable;

    /**
     * @var \Generator<mixed, TValue, mixed, mixed>
     */
    private \Generator $generator;

    /**
     * @param callable(): \Generator<mixed, TValue, mixed, mixed> $callable
     */
    public function __construct(callable $callable)
    {
        $this->callable = $callable(...);

        $this->recreateGenerator();
    }

    private function recreateGenerator(): void
    {
        $this->generator = ($this->callable)();
    }

    /**
     * @return TValue
     */
    public function current(): mixed
    {
        return $this->generator->current();
    }

    public function next(): void
    {
        $this->generator->next();
    }

    public function key(): mixed
    {
        return $this->generator->key();
    }

    public function valid(): bool
    {
        return $this->generator->valid();
    }

    public function rewind(): void
    {
        $this->recreateGenerator();
    }
}
