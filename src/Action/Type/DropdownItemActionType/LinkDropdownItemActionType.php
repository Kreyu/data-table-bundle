<?php
declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Action\Type\DropdownItemActionType;

use Kreyu\Bundle\DataTableBundle\Action\Type\AbstractActionType;
use Kreyu\Bundle\DataTableBundle\Action\Type\LinkActionType;

class LinkDropdownItemActionType extends AbstractActionType
{
    public function getParent(): ?string
    {
        return LinkActionType::class;
    }
}