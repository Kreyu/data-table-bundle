<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Unit\Util;

use Kreyu\Bundle\DataTableBundle\Util\RewindableGeneratorIterator;
use PHPUnit\Framework\TestCase;

class RewindableGeneratorIteratorTest extends TestCase
{
    public function testRewind()
    {
        $calls = 0;

        $callable = function () use (&$calls) {
            ++$calls;
            yield from [1, 2, 3];
        };

        $iterator = new RewindableGeneratorIterator($callable);
        $iterator->next(); // 2
        $iterator->next(); // 3
        $iterator->rewind(); // 1

        $this->assertEquals(1, $iterator->current());
        $this->assertSame(2, $calls);
    }
}
