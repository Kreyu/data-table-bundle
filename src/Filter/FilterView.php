<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Filter;

use Kreyu\Bundle\DataTableBundle\DataTableView;

class FilterView
{
    public array $vars = [
        'attr' => [],
    ];

    public function __construct(
        public ?DataTableView $parent = null,
    ) {
    }

    public function getFormName(): string
    {
        return $this->vars['form_name'];
    }

    public function getFormOptions(): array
    {
        return [
            'label' => $this->vars['label'],
            'translation_domain' => $this->vars['translation_domain'],
            'field_type' => $this->vars['field_type'],
            'field_options' => $this->vars['field_options'],
            'operator_type' => $this->vars['operator_type'],
            'operator_options' => $this->vars['operator_options'],
        ];
    }
}
