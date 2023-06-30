<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Action;

interface ActionBuilderInterface extends ActionConfigBuilderInterface
{
    public function getAction(): ActionInterface;
}
