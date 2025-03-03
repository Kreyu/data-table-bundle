<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\IdentifierGenerator;

interface DataTableTurboIdentifierGeneratorInterface
{
    public function generate(string $dataTableName): string;
}
