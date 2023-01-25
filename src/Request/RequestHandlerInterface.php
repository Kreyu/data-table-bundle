<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Request;

use Kreyu\Bundle\DataTableBundle\DataTableInterface;

interface RequestHandlerInterface
{
    public function handle(DataTableInterface $dataTable, mixed $request = null): void;
}