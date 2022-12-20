<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column\View\Factory;

use Kreyu\Bundle\DataTableBundle\Column\ColumnInterface;
use Kreyu\Bundle\DataTableBundle\Column\View\ColumnView;
use Kreyu\Bundle\DataTableBundle\Column\View\ColumnViewInterface;
use Kreyu\Bundle\DataTableBundle\View\DataTableViewInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

class ColumnValueViewFactory implements ColumnValueViewFactoryInterface
{
    private PropertyAccessorInterface $propertyAccessor;

    public function __construct()
    {
        $this->propertyAccessor = PropertyAccess::createPropertyAccessor();
    }

    public function create(DataTableViewInterface $dataTable, ColumnInterface $column, mixed $value): ColumnViewInterface
    {
        $view = new ColumnView();
        $view->setVariable('data_table', $dataTable);

        $options = $column->getOptions();
        $options['property_path'] ??= $column->getName();

        if (is_array($value) || is_object($value)) {
            $value = $this->propertyAccessor->getValue($value, $options['property_path']);
        }

        foreach ($options as $optionKey => $optionValue) {
            if ($optionValue instanceof \Closure) {
                $optionValue = $optionValue($value);
            }

            $view->setVariable($optionKey, $optionValue);
        }

        if (!$view->getVariable('value')) {
            $view->setVariable('value', $value);
        }

        $column->getType()->buildValueView($view, $value);

        return $view;
    }
}
