<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\RowGroup;

class RowGroupData
{
    /**
     * @param list<string> $fields
     */
    public function __construct(
        private array $fields = [],
        private bool $enabled = true,
    ) {
    }

    /**
     * @return list<string>
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * @param list<string> $fields
     */
    public function setFields(array $fields): void
    {
        $this->fields = $fields;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
    }
}
