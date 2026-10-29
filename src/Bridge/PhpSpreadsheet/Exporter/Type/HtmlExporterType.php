<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Bridge\PhpSpreadsheet\Exporter\Type;

use PhpOffice\PhpSpreadsheet\Shared\StringHelper;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Html;
use PhpOffice\PhpSpreadsheet\Writer\IWriter;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class HtmlExporterType extends AbstractPhpSpreadsheetExporterType
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
        /** @var string $decimalSeparator */
        $decimalSeparator = $options['decimal_separator'];

        /** @var string $thousandsSeparator */
        $thousandsSeparator = $options['thousands_separator'];

        /** @var string $imagesRoot */
        $imagesRoot = $options['images_root'];

        /** @var bool $embedImages */
        $embedImages = $options['embed_images'];

        /** @var bool $useInlineCss */
        $useInlineCss = $options['use_inline_css'];

        /** @var bool $generateSheetNavigationBlock */
        $generateSheetNavigationBlock = $options['generate_sheet_navigation_block'];

        /** @var int|null $sheetIndex */
        $sheetIndex = $options['sheet_index'];

        /** @var callable|null $editHtmlCallback */
        $editHtmlCallback = $options['edit_html_callback'];

        StringHelper::setDecimalSeparator($decimalSeparator);
        StringHelper::setThousandsSeparator($thousandsSeparator);

        $writer = (new Html($spreadsheet))
            ->setImagesRoot($imagesRoot)
            ->setEmbedImages($embedImages)
            ->setUseInlineCss($useInlineCss)
            ->setGenerateSheetNavigationBlock($generateSheetNavigationBlock)
        ;

        $writer->setEditHtmlCallback($editHtmlCallback);

        if ($sheetIndex === null) {
            $writer->writeAllSheets();

            return $writer;
        }

        $writer->setSheetIndex($sheetIndex);

        return $writer;
    }
}
