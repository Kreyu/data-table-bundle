<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Action;

use Kreyu\Bundle\DataTableBundle\Action\Type\ActionType;
use Kreyu\Bundle\DataTableBundle\Action\Type\ActionTypeInterface;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;

interface ActionFactoryInterface
{
    /**
     * @param class-string<ActionTypeInterface> $type
     *
     * @throws InvalidOptionsException if any of given option is not applicable to the given type
     */
    public function create(string $type = ActionType::class, array $options = []): ActionInterface;

    /**
     * @param class-string<ActionTypeInterface> $type
     *
     * @throws InvalidOptionsException if any of given option is not applicable to the given type
     */
    public function createNamed(string $name, string $type = ActionType::class, array $options = []): ActionInterface;

    /**
     * @param class-string<ActionTypeInterface> $type
     *
     * @throws InvalidOptionsException if any of given option is not applicable to the given type
     */
    public function createBuilder(string $type = ActionType::class, array $options = []): ActionBuilderInterface;

    /**
     * @param class-string<ActionTypeInterface> $type
     *
     * @throws InvalidOptionsException if any of given option is not applicable to the given type
     */
    public function createNamedBuilder(string $name, string $type = ActionType::class, array $options = []): ActionBuilderInterface;
}
