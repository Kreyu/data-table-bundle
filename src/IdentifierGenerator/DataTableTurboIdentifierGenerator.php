<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\IdentifierGenerator;

class DataTableTurboIdentifierGenerator implements DataTableTurboIdentifierGeneratorInterface
{
    public function generate(string $dataTableName): string
    {
        return 'kreyu_data_table_'.$dataTableName;
    }
}
