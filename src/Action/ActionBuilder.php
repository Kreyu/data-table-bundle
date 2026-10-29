<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Action;

class ActionBuilder extends ActionConfigBuilder implements ActionBuilderInterface
{
    public function getAction(): ActionInterface
    {
        $this->ensureNotLocked();

        return new Action($this->getActionConfig());
    }
}
