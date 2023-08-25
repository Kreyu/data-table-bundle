<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Action\Type;

use Kreyu\Bundle\DataTableBundle\Test\ActionTypeTestCase;

class LinkActionTypeTest extends ActionTypeTestCase
{
    public const TESTED_TYPE = 'Kreyu\Bundle\DataTableBundle\Action\Type\LinkActionType';

    protected function getTestedType(): string
    {
        return static::TESTED_TYPE;
    }
}