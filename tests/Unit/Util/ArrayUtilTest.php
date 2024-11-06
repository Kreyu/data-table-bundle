<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Unit\Util;

use Kreyu\Bundle\DataTableBundle\Util\ArrayUtil;
use PHPUnit\Framework\TestCase;

class ArrayUtilTest extends TestCase
{
    public function testKsort()
    {
        $input = ['c' => 3, 'b' => 2, 'a' => 1];
        $output = ArrayUtil::ksort($input);

        $this->assertSame(['a' => 1, 'b' => 2, 'c' => 3], $output);
        $this->assertSame(['c' => 3, 'b' => 2, 'a' => 1], $input);
    }

    public function testMapWithKeys()
    {
        $input = [
            ['id' => 1, 'name' => 'John'],
            ['id' => 2, 'name' => 'Jane'],
        ];

        $output = ArrayUtil::mapWithKeys(
            callback: fn (array $array) => [$array['id'] => $array['name']],
            array: $input,
        );

        $this->assertSame([1 => 'John', 2 => 'Jane'], $output);
        $this->assertSame([
            ['id' => 1, 'name' => 'John'],
            ['id' => 2, 'name' => 'Jane'],
        ], $input);
    }
}
