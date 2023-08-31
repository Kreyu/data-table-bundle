<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Filter;

use Kreyu\Bundle\DataTableBundle\Exception\BadMethodCallException;
use Kreyu\Bundle\DataTableBundle\Filter\Form\Type\OperatorType;
use Kreyu\Bundle\DataTableBundle\Filter\Type\ResolvedFilterTypeInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class FilterBuilder extends FilterConfigBuilder implements FilterBuilderInterface
{
    public function getFilter(): FilterInterface
    {
        if ($this->locked) {
            throw $this->createBuilderLockedException();
        }

        return new Filter($this->getFilterConfig());
    }

    private function createBuilderLockedException(): BadMethodCallException
    {
        return new BadMethodCallException('FilterBuilder methods cannot be accessed anymore once the builder is turned into a FilterConfigInterface instance.');
    }
}
