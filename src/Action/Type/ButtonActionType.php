<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Action\Type;

final class ButtonActionType extends AbstractActionType
{
    public function getParent(): ?string
    {
        return LinkActionType::class;
    }
}
