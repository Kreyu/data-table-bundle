<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Bridge\PhpSpreadsheet\Exporter\Type;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\IWriter;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\OptionsResolver\OptionsResolver;

class XlsxExporterType extends AbstractExporterType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'office_2003_compatibility' => false,
            ])
            ->setAllowedTypes('office_2003_compatibility', 'bool')
        ;
    }

    protected function getWriter(Spreadsheet $spreadsheet, array $options): IWriter
    {
        return (new Xlsx($spreadsheet))
            ->setOffice2003Compatibility($options['office_2003_compatibility'])
        ;
    }
}
