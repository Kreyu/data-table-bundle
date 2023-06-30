<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Action;

use Kreyu\Bundle\DataTableBundle\Action\Type\ActionType;

class ActionFactory implements ActionFactoryInterface
{
    public function __construct(
        private readonly ActionRegistryInterface $registry,
    ) {
    }

    public function create(string $type = ActionType::class, array $options = []): ActionInterface
    {
        return $this->createBuilder($type, $options)->getAction();
    }

    public function createNamed(string $name, string $type = ActionType::class, array $options = []): ActionInterface
    {
        return $this->createNamedBuilder($name, $type, $options)->getAction();
    }

    public function createBuilder(string $type = ActionType::class, array $options = []): ActionBuilderInterface
    {
        return $this->createNamedBuilder($this->registry->getType($type)->getBlockPrefix(), $type, $options);
    }

    public function createNamedBuilder(string $name, string $type = ActionType::class, array $options = []): ActionBuilderInterface
    {
        $type = $this->registry->getType($type);

        $builder = $type->createBuilder($this, $name, $options);

        $type->buildAction($builder, $builder->getOptions());

        return $builder;
    }
}
