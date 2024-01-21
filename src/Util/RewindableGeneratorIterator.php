<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Util;

class RewindableGeneratorIterator implements \Iterator
{
    private \Closure $callable;
    private \Generator $generator;

    /**
     * @param callable(): \Generator $callable
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
