<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Query;

interface ResultSetInterface extends \IteratorAggregate, \Countable
{
    public function getCurrentPageItemCount(): ?int;

    public function getTotalItemCount(): ?int;
}
