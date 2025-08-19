<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Formatter;

use Kreyu\Bundle\DataTableBundle\Filter\FilterData;
use Kreyu\Bundle\DataTableBundle\Filter\FilterInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;

class EntityActiveFilterFormatter
{
    public function __invoke(FilterData $data, FilterInterface $filter)
    {
        $choiceLabel = $filter->getConfig()->getOption('choice_label');

        if (is_string($choiceLabel)) {
            return PropertyAccess::createPropertyAccessor()->getValue($data->getValue(), $choiceLabel);
        }

        if (is_callable($choiceLabel)) {
            return $choiceLabel($data->getValue());
        }

        return (string) $data->getValue();
    }
}
