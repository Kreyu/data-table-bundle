<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\RowGroup;

use Kreyu\Bundle\DataTableBundle\DataTableView;

class RowGroupFooterView
{
    public array $vars = [
        'attr' => [],
        'row_type' => 'group_footer',
    ];

    /**
     * @param DataTableView          $parent
     * @param mixed                  $groupValue      The value that identifies this group
     * @param string                 $fieldName       The field name used for grouping
     * @param int                    $level           Nesting level (0-based) for multi-level grouping
     * @param int                    $columnCount     Number of columns in the table (for colspan)
     * @param list<AggregationResult> $aggregations   Computed aggregation results
     * @param int                    $rowCount        Number of data rows in this group
     */
    public function __construct(
        public readonly DataTableView $parent,
        public readonly mixed $groupValue,
        public readonly string $fieldName,
        public readonly int $level = 0,
        public readonly int $columnCount = 0,
        public readonly array $aggregations = [],
        public readonly int $rowCount = 0,
    ) {
        $this->vars['group_value'] = $groupValue;
        $this->vars['field_name'] = $fieldName;
        $this->vars['level'] = $level;
        $this->vars['column_count'] = $columnCount;
        $this->vars['aggregations'] = $aggregations;
        $this->vars['row_count'] = $rowCount;
    }
}
