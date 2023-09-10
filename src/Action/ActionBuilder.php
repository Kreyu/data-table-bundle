<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Action;

use Kreyu\Bundle\DataTableBundle\Exception\BadMethodCallException;

class ActionBuilder extends ActionConfigBuilder implements ActionBuilderInterface
{
    public function getAction(): ActionInterface
    {
        if ($this->locked) {
            throw $this->createBuilderLockedException();
        }

        return new Action($this->getActionConfig());
    }

    private function createBuilderLockedException(): BadMethodCallException
    {
        return new BadMethodCallException('ActionBuilder methods cannot be accessed anymore once the builder is turned into a ActionConfigInterface instance.');
    }
}
