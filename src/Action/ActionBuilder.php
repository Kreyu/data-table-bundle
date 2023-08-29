<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Action;

use Kreyu\Bundle\DataTableBundle\Action\Type\ResolvedActionTypeInterface;
use Kreyu\Bundle\DataTableBundle\Exception\BadMethodCallException;

class ActionBuilder extends ActionConfigBuilder implements ActionBuilderInterface
{
    public function getAction(): ActionInterface
    {
        return new Action($this->getActionConfig());
    }
}
