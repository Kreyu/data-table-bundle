<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Bridge\OpenSpout\Exporter\Type;

use Kreyu\Bundle\DataTableBundle\Column\ColumnHeaderView;
use Kreyu\Bundle\DataTableBundle\DataTableView;
use Kreyu\Bundle\DataTableBundle\Exporter\ExportFile;
use Kreyu\Bundle\DataTableBundle\Exporter\Type\AbstractExporterType as BaseAbstractExporterType;
use Kreyu\Bundle\DataTableBundle\HeaderRowView;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Exception\IOException;
use OpenSpout\Writer\Exception\WriterNotOpenedException;
use OpenSpout\Writer\WriterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

abstract class AbstractExporterType extends BaseAbstractExporterType
{
    public function __construct(
        private ?TranslatorInterface $translator = null
    ) {
    }

    /**
     * @throws IOException
     * @throws WriterNotOpenedException
     */
    public function export(DataTableView $view, string $filename, array $options = []): ExportFile
    {
        if (!class_exists(Row::class)) {
            throw new \LogicException('Trying to use exporter that requires OpenSpout which is not installed. Try running "composer require openspout/openspout".');
        }

        touch($path = $this->getTempnam($options));

        $writer = $this->getWriter($options);
        $writer->openToFile($path);

        if ($options['use_headers']) {
            /** @var HeaderRowView $headerRow */
            $headerRow = $view->vars['header_row'];

            $labels = [];

            foreach ($headerRow->children as $child) {
                if (false !== $child->vars['export']) {
                    $label = $child->vars['export']['label'];

                    if ($this->translator && $translationDomain = $child->vars['export']['translation_domain'] ?? null) {
                        $label = $this->translator->trans($label, $child->vars['export']['translation_parameters'] ?? [], $translationDomain);
                    }

                    $labels[] = $label;
                }
            }

            $writer->addRow(Row::fromValues($labels));
        }

        foreach ($view->vars['value_rows'] as $valueRow) {
            $values = [];

            foreach ($valueRow->children as $child) {
                if (false !== $child->vars['export']) {
                    $values[] = $child->vars['export']['value'];
                }
            }

            $writer->addRow(Row::fromValues($values));
        }

        $writer->close();

        $extension = $this->getExtension();

        return new ExportFile($path, "$filename.$extension");
    }

    protected abstract function getExtension(): string;

    protected abstract function getWriter(array $options): WriterInterface;
}