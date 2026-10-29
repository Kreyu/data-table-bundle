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
        /** @var string|callable|null $choiceLabel */
        $choiceLabel = $filter->getConfig()->getOption('choice_label');

        if (is_string($choiceLabel)) {
            /** @var object|array<array-key, mixed> $entity */
            $entity = $data->getValue();

            return PropertyAccess::createPropertyAccessor()->getValue($entity, $choiceLabel);
        }

        if (is_callable($choiceLabel)) {
            return $choiceLabel($data->getValue());
        }

        return (string) $data->getValue();
    }
}
