<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Action\Type;

use Kreyu\Bundle\DataTableBundle\Action\Type\AbstractActionType;

class RecursiveActionTypeBaz extends AbstractActionType
{
    public function getParent(): string
    {
        return RecursiveActionTypeFoo::class;
    }
}
