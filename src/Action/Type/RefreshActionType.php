<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Action\Type;

use Kreyu\Bundle\DataTableBundle\Action\ActionContext;
use Kreyu\Bundle\DataTableBundle\Action\ActionInterface;
use Kreyu\Bundle\DataTableBundle\Action\ActionRefreshUrlGeneratorInterface;
use Kreyu\Bundle\DataTableBundle\Action\ActionView;

class RefreshActionType extends AbstractActionType
{
    public function __construct(
        private ActionRefreshUrlGeneratorInterface $columnRefreshUrlGenerator,
    ) {
    }

    public function buildView(ActionView $view, ActionInterface $action, array $options): void
    {
        if (ActionContext::Global !== $action->getConfig()->getContext()) {
            throw new \LogicException(sprintf('A %s action can only be added as a global action.', $this::class));
        }

        $view->vars = array_replace($view->vars, [
            'href' => $this->columnRefreshUrlGenerator->generate(),
        ]);
    }
}
