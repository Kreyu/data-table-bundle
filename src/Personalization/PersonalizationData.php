<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Personalization;

use Kreyu\Bundle\DataTableBundle\Column\ColumnInterface;
use Kreyu\Bundle\DataTableBundle\DataTableInterface;
use Kreyu\Bundle\DataTableBundle\Exception\InvalidArgumentException;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PersonalizationData
{
    private static ?OptionsResolver $optionsResolver = null;

    /**
     * @var array<string, PersonalizationColumnData>
     */
    private array $columns = [];

    /**
     * @param array<ColumnInterface|PersonalizationColumnData> $columns
     */
    public function __construct(array $columns = [])
    {
        foreach ($columns as $column) {
            $this->addColumn($column);
        }
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $resolver = self::getOptionsResolver();

        /** @var array{columns: array<array{name: string, priority: int, visible: bool}>} $resolvedData */
        $resolvedData = $resolver->resolve($data);

        return new self(array_map(
            PersonalizationColumnData::fromArray(...),
            $resolvedData['columns'],
        ));
    }

    public static function fromDataTable(DataTableInterface $dataTable): self
    {
        return new self(array_filter(
            $dataTable->getColumns(),
            static fn (ColumnInterface $column) => $column->getConfig()->isPersonalizable(),
        ));
    }

    /**
     * @return array<string, PersonalizationColumnData>
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    public function hasColumn(string|ColumnInterface|PersonalizationColumnData $column): bool
    {
        if (!is_string($column)) {
            $column = $column->getName();
        }

        return array_key_exists($column, $this->columns);
    }

    public function getColumn(string|ColumnInterface|PersonalizationColumnData $column): ?PersonalizationColumnData
    {
        if (!is_string($column)) {
            $column = $column->getName();
        }

        return $this->columns[$column] ?? null;
    }

    public function addColumn(ColumnInterface|PersonalizationColumnData $column): void
    {
        if ($column instanceof ColumnInterface) {
            if (!$column->getConfig()->isPersonalizable()) {
                throw new InvalidArgumentException('Unable to add non-personalizable column');
            }

            $column = PersonalizationColumnData::fromColumn($column);
        }

        $this->columns[$column->getName()] = $column;
    }

    public function removeColumn(string|ColumnInterface|PersonalizationColumnData $column): void
    {
        if (!is_string($column)) {
            $column = $column->getName();
        }

        unset($this->columns[$column]);
    }

    /**
     * @param array<ColumnInterface|PersonalizationColumnData> $columns
     */
    public function addMissingColumns(array $columns): void
    {
        foreach ($columns as $column) {
            if ($column instanceof ColumnInterface && !$column->getConfig()->isPersonalizable()) {
                continue;
            }

            if (!$this->hasColumn($column)) {
                $this->addColumn($column);
            }
        }
    }

    /**
     * @param array<ColumnInterface|PersonalizationColumnData> $columns
     */
    public function removeRedundantColumns(array $columns): void
    {
        foreach (array_diff_key($this->columns, $columns) as $column) {
            $this->removeColumn($column);
        }

        foreach ($columns as $column) {
            if (!($column instanceof ColumnInterface && !$column->getConfig()->isPersonalizable())) {
                continue;
            }

            $this->removeColumn($column);
        }
    }

    private static function getOptionsResolver(): OptionsResolver
    {
        return self::$optionsResolver ??= self::createOptionsResolver();
    }

    private static function createOptionsResolver(): OptionsResolver
    {
        $configureColumns = static function (OptionsResolver $resolver): void {
            $resolver
                ->setPrototype(true)
                ->setDefaults([
                    'name' => null,
                    'priority' => 0,
                    'visible' => true,
                ])
                ->setAllowedTypes('name', ['null', 'string'])
                ->setAllowedTypes('priority', 'int')
                ->setAllowedTypes('visible', 'bool')
            ;
        };

        $resolver = (new OptionsResolver())
            ->setDefined('columns')
            ->addNormalizer('columns', self::normalizeColumnNames(...))
        ;

        $supportsNestedOptions = method_exists($resolver, 'setOptions');

        if ($supportsNestedOptions) {
            return $resolver->setOptions('columns', $configureColumns);
        }

        return $resolver->setDefault('columns', $configureColumns);
    }

    /**
     * @param array<array-key, array<string, mixed>> $columns
     *
     * @return array<array-key, array<string, mixed>>
     */
    private static function normalizeColumnNames(Options $options, array $columns): array
    {
        foreach ($columns as $name => $column) {
            $column['name'] ??= $name;
            $columns[$name] = $column;
        }

        return $columns;
    }
}
