<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Filter;

interface FilterBuilderInterface extends FilterConfigBuilderInterface
{
    public function getFilter(): FilterInterface;
}
