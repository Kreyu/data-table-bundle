<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column\Factory;

use Kreyu\Bundle\DataTableBundle\Column\Column;
use Kreyu\Bundle\DataTableBundle\Column\ColumnInterface;
use Kreyu\Bundle\DataTableBundle\Column\Type\ColumnFactoryAwareTypeInterface;
use Kreyu\Bundle\DataTableBundle\Column\Type\ColumnTypeChain;
use Kreyu\Bundle\DataTableBundle\Column\Type\ColumnTypeInterface;
use Symfony\Component\Form\Exception\InvalidArgumentException;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ColumnFactory implements ColumnFactoryInterface
{
    public function __construct(
        private readonly ColumnTypeChain $columnTypeChain,
    ) {
    }

    /**
     * @param class-string<ColumnTypeInterface> $typeClass
     */
    public function create(string $name, string $typeClass, array $options = []): ColumnInterface
    {
        if (null === $type = $this->columnTypeChain->get($typeClass)) {
            throw new InvalidArgumentException(sprintf('Could not load type "%s".', $typeClass));
        }

        if ($type instanceof ColumnFactoryAwareTypeInterface) {
            $type->setColumnFactory($this);
        }

        $type->configureOptions($optionsResolver = new OptionsResolver());

        return new Column($name, $type, $optionsResolver->resolve($options));
    }
}
