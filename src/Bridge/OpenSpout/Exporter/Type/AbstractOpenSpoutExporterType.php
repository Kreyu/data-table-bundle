<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Bridge\OpenSpout\Exporter\Type;

use Kreyu\Bundle\DataTableBundle\Column\ColumnHeaderView;
use Kreyu\Bundle\DataTableBundle\Column\ColumnValueView;
use Kreyu\Bundle\DataTableBundle\DataTableView;
use Kreyu\Bundle\DataTableBundle\Exception\UnexpectedTypeException;
use Kreyu\Bundle\DataTableBundle\Exporter\ExporterInterface;
use Kreyu\Bundle\DataTableBundle\Exporter\ExportFile;
use Kreyu\Bundle\DataTableBundle\Exporter\Type\AbstractExporterType;
use Kreyu\Bundle\DataTableBundle\HeaderRowView;
use Kreyu\Bundle\DataTableBundle\ValueRowView;
use OpenSpout\Common\Entity\Cell;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Common\Exception\IOException;
use OpenSpout\Writer\Exception\WriterNotOpenedException;
use OpenSpout\Writer\WriterInterface;

abstract class AbstractOpenSpoutExporterType extends AbstractExporterType
{
    /**
     * @param array<string, mixed> $options
     *
     * @throws IOException
     * @throws WriterNotOpenedException
     */
    public function export(
        DataTableView $view,
        ExporterInterface $exporter,
        string $filename,
        array $options = [],
    ): ExportFile {
        touch($path = $this->getTempnam($options));

        $writer = $this->getWriter($options);
        $writer->openToFile($path);

        /** @var bool $useHeaders */
        $useHeaders = $options['use_headers'];
        $headerRow = $useHeaders ? $view->headerRow : null;

        if ($headerRow !== null) {
            $writer->addRow(new Row(
                cells: $this->getHeaderRowCells($headerRow, $options),
                style: $this->getStyle($headerRow, 'header_row_style', $options),
            ));
        }

        foreach ($view->valueRows as $valueRow) {
            $writer->addRow(new Row(
                cells: $this->getValueRowCells($valueRow, $options),
                style: $this->getStyle($valueRow, 'value_row_style', $options),
            ));
        }

        $writer->close();

        return new ExportFile($path, sprintf('%s.%s', $filename, $this->getExtension()));
    }

    public function getParent(): ?string
    {
        return OpenSpoutExporterType::class;
    }

    protected function getWriter(array $options): WriterInterface
    {
        $writerClass = $this->getWriterClass();

        return new $writerClass($this->getWriterOptions($options));
    }

    abstract protected function getExtension(): string;

    /**
     * @return class-string<WriterInterface>
     */
    abstract protected function getWriterClass(): string;

    abstract protected function getWriterOptions(array $options): mixed;

    /**
     * @return array<Cell>
     */
    private function getHeaderRowCells(HeaderRowView $view, array $options): array
    {
        return array_map(
            fn (ColumnHeaderView $columnHeaderView) => $this->getHeaderCell($columnHeaderView, $options),
            $view->children,
        );
    }

    protected function getHeaderCell(ColumnHeaderView $view, array $options): Cell
    {
        return Cell::fromValue(
            value: $this->getCellValue($view->vars['label']),
            style: $this->getStyle($view, 'header_cell_style', $options),
        );
    }

    /**
     * @return array<Cell>
     */
    protected function getValueRowCells(ValueRowView $view, array $options): array
    {
        return array_map(
            fn (ColumnValueView $columnValueView) => $this->getValueCell($columnValueView, $options),
            $view->children,
        );
    }

    protected function getValueCell(ColumnValueView $view, array $options): Cell
    {
        return Cell::fromValue(
            value: $this->getCellValue($view->vars['value']),
            style: $this->getStyle($view, 'value_cell_style', $options),
        );
    }

    protected function getStyle(mixed $view, string $optionName, array $options): Style
    {
        /** @var Style|(callable(mixed, array<array-key, mixed>): (Style|null))|null $style */
        $style = $options[$optionName] ?? null;

        if (is_callable($style)) {
            return $style($view, $options) ?? new Style();
        }

        return $style ?? new Style();
    }

    private function getCellValue(mixed $value): bool|\DateInterval|\DateTimeInterface|float|int|string|null
    {
        if ($value instanceof \Stringable) {
            return (string) $value;
        }

        if ($value instanceof \DateTimeInterface) {
            return $value;
        }

        if ($value instanceof \DateInterval) {
            return $value;
        }

        if ($value === null) {
            return null;
        }

        $isScalar = is_scalar($value);

        if ($isScalar) {
            return $value;
        }

        throw new UnexpectedTypeException($value, 'scalar, \DateTimeInterface, \DateInterval or null');
    }
}
