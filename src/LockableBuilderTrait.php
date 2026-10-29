<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle;

use Kreyu\Bundle\DataTableBundle\Exception\BadMethodCallException;

trait LockableBuilderTrait
{
    protected bool $locked = false;

    protected function ensureNotLocked(): void
    {
        if (!$this->locked) {
            return;
        }

        throw new BadMethodCallException(sprintf(
            '"%s" methods cannot be accessed anymore once the builder is turned into a config instance.',
            get_debug_type($this),
        ));
    }
}
