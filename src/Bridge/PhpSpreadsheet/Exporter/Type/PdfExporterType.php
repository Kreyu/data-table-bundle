<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Bridge\PhpSpreadsheet\Exporter\Type;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Writer\IWriter;
use PhpOffice\PhpSpreadsheet\Writer\Pdf\Dompdf;
use PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf;
use PhpOffice\PhpSpreadsheet\Writer\Pdf\Tcpdf;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PdfExporterType extends AbstractExporterType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'orientation' => PageSetup::ORIENTATION_DEFAULT,
            ])
            ->setRequired('library')
            ->setAllowedTypes('orientation', 'string')
            ->setAllowedTypes('library', 'string')
            ->setAllowedValues('orientation', [
                PageSetup::ORIENTATION_DEFAULT,
                PageSetup::ORIENTATION_LANDSCAPE,
                PageSetup::ORIENTATION_PORTRAIT,
            ])
            ->setAllowedValues('library', ['dompdf', 'mpdf', 'tcpdf'])
        ;
    }

    public function getParent(): ?string
    {
        return HtmlExporterType::class;
    }

    protected function getWriter(Spreadsheet $spreadsheet, array $options): IWriter
    {
        $writer = match ($options['library']) {
            'dompdf' => new Dompdf($spreadsheet),
            'mpdf' => new Mpdf($spreadsheet),
            'tcpdf' => new Tcpdf($spreadsheet),
        };

        $writer->setOrientation($options['orientation']);

        return $writer;
    }
}
