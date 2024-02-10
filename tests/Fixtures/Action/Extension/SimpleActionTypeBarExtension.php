<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Action\Extension;

use Kreyu\Bundle\DataTableBundle\Action\Extension\AbstractActionTypeExtension;
use Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Action\Type\SimpleActionType;

class SimpleActionTypeBarExtension extends AbstractActionTypeExtension
{
    public static function getExtendedTypes(): iterable
    {
        yield SimpleActionType::class;
    }
}
