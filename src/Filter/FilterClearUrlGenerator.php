<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Filter;

use Kreyu\Bundle\DataTableBundle\DataTableView;
use Kreyu\Bundle\DataTableBundle\Request\AbstractUrlGenerator;

class FilterClearUrlGenerator extends AbstractUrlGenerator implements FilterClearUrlGeneratorInterface
{
    public function generate(DataTableView $dataTableView, FilterView ...$filterViews): string
    {
        $parameters = $this->getUrlParameters($dataTableView);

        foreach ($filterViews as $filterView) {
            $parameters = array_replace_recursive($parameters, $this->getFilterClearQueryParameters($filterView));
        }

        $parameters = [...$parameters, ...$this->getFirstPageParameters($dataTableView)];

        return $this->generateUrl($parameters);
    }

    /**
     * @return array<string, array<string, array<string, mixed>>>
     */
    private function getFilterClearQueryParameters(FilterView $filterView): array
    {
        $parameters = ['value' => $this->getClearedValue($filterView->data?->getValue())];

        /** @var bool $operatorSelectable */
        $operatorSelectable = $filterView->vars['operator_selectable'];

        if ($operatorSelectable) {
            $parameters['operator'] = null;
        }

        /** @var string $filtrationParameterName */
        $filtrationParameterName = $filterView->parent->vars['filtration_parameter_name'];

        /** @var string $formName */
        $formName = $filterView->vars['form_name'];

        return [
            $filtrationParameterName => [
                $formName => $parameters,
            ],
        ];
    }

    /**
     * @return string|array<array-key, string>
     */
    private function getClearedValue(mixed $value): string|array
    {
        if (!is_array($value)) {
            return '';
        }

        return array_map(static fn (): string => '', $value);
    }
}
