<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Action\Extension;

use Kreyu\Bundle\DataTableBundle\Action\ActionInterface;
use Kreyu\Bundle\DataTableBundle\Action\ActionView;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractActionTypeExtension implements ActionTypeExtensionInterface
{
    public function buildView(ActionView $view, ActionInterface $action, array $options): void
    {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
    }
}
