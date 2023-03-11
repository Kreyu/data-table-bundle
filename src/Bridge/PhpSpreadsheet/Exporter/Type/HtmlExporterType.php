<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Bridge\PhpSpreadsheet\Exporter\Type;

use PhpOffice\PhpSpreadsheet\Shared\StringHelper;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Html;
use PhpOffice\PhpSpreadsheet\Writer\IWriter;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HtmlExporterType extends AbstractExporterType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'sheet_index' => 0,
                'images_root' => '',
                'embed_images' => false,
                'use_inline_css' => false,
                'generate_sheet_navigation_block' => true,
                'edit_html_callback' => null,
                'decimal_separator' => StringHelper::getDecimalSeparator(),
                'thousands_separator' => StringHelper::getThousandsSeparator(),
            ])
            ->setAllowedTypes('sheet_index', ['null', 'int'])
            ->setAllowedTypes('images_root', 'string')
            ->setAllowedTypes('embed_images', 'bool')
            ->setAllowedTypes('use_inline_css', 'bool')
            ->setAllowedTypes('generate_sheet_navigation_block', 'bool')
            ->setAllowedTypes('edit_html_callback', ['null', 'callable'])
            ->setAllowedTypes('decimal_separator', 'string')
            ->setAllowedTypes('thousands_separator', 'string')
        ;
    }

    protected function getWriter(Spreadsheet $spreadsheet, array $options): IWriter
    {
        StringHelper::setDecimalSeparator($options['decimal_separator']);
        StringHelper::setThousandsSeparator($options['thousands_separator']);

        $writer = (new Html($spreadsheet))
            ->setImagesRoot($options['images_root'])
            ->setEmbedImages($options['embed_images'])
            ->setUseInlineCss($options['use_inline_css'])
            ->setGenerateSheetNavigationBlock($options['generate_sheet_navigation_block'])
        ;

        if (null !== $options['sheet_index']) {
            $writer->setSheetIndex($options['sheet_index']);
        } else {
            $writer->writeAllSheets();
        }

        $writer->setEditHtmlCallback($options['edit_html_callback']);

        return $writer;
    }
}
