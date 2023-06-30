<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Action\Extension;

use Kreyu\Bundle\DataTableBundle\Action\ActionBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Action\ActionInterface;
use Kreyu\Bundle\DataTableBundle\Action\ActionView;
use Kreyu\Bundle\DataTableBundle\Action\Type\ActionTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

interface ActionTypeExtensionInterface
{
    public function buildAction(ActionBuilderInterface $builder, array $options): void;

    public function buildView(ActionView $view, ActionInterface $action, array $options): void;

    public function configureOptions(OptionsResolver $resolver): void;

    /**
     * @return iterable<class-string<ActionTypeInterface>>
     */
    public static function getExtendedTypes(): iterable;
}
