<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Unit\Responsive;

use Kreyu\Bundle\DataTableBundle\Responsive\Device;
use PHPUnit\Framework\TestCase;

class DeviceTest extends TestCase
{
    public function testStringValues(): void
    {
        $this->assertSame('phone', Device::Phone->value);
        $this->assertSame('tablet', Device::Tablet->value);
        $this->assertSame('desktop', Device::Desktop->value);
    }

    public function testTryFromValidValues(): void
    {
        $this->assertSame(Device::Phone, Device::tryFrom('phone'));
        $this->assertSame(Device::Tablet, Device::tryFrom('tablet'));
        $this->assertSame(Device::Desktop, Device::tryFrom('desktop'));
    }

    public function testTryFromInvalidValue(): void
    {
        $this->assertNull(Device::tryFrom('invalid'));
        $this->assertNull(Device::tryFrom(''));
    }
}
