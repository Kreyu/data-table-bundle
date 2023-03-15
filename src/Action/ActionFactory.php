<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Action;

use Kreyu\Bundle\DataTableBundle\Action\Type\ActionTypeInterface;

class ActionFactory implements ActionFactoryInterface
{
    public function __construct(
        private ActionRegistryInterface $registry,
    ) {
    }

    /**
     * @param class-string<ActionTypeInterface> $type
     */
    public function create(string $name, string $type, array $options = []): ActionInterface
    {
        $type = $this->registry->getType($type);

        $optionsResolver = $type->getOptionsResolver();

        return new Action($name, $type, $optionsResolver->resolve($options));
    }
}
