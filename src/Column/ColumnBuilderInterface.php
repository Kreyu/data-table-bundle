<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column;

interface ColumnBuilderInterface extends ColumnConfigBuilderInterface
{
    public function getColumn(): ColumnInterface;
}
