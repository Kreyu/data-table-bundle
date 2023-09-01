<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Action\Type;

use Kreyu\Bundle\DataTableBundle\Action\ActionBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Action\ActionInterface;
use Kreyu\Bundle\DataTableBundle\Action\ActionView;
use Symfony\Component\OptionsResolver\OptionsResolver;

interface ActionTypeInterface
{
    public function buildAction(ActionBuilderInterface $builder, array $options = []): void;

    public function buildView(ActionView $view, ActionInterface $action, array $options): void;

    public function configureOptions(OptionsResolver $resolver): void;

    public function getBlockPrefix(): string;

    /**
     * @return class-string<ActionTypeInterface>|null
     */
    public function getParent(): ?string;
}
