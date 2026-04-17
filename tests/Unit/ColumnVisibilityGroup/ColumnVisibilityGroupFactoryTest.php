<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Unit\ColumnVisibilityGroup;

use Kreyu\Bundle\DataTableBundle\ColumnVisibilityGroup\ColumnVisibilityGroupFactory;
use PHPUnit\Framework\TestCase;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException;
use Symfony\Contracts\Translation\TranslatorInterface;

class ColumnVisibilityGroupFactoryTest extends TestCase
{
    public function testCreateWithoutOptions()
    {
        $group = $this->createFactory()->create('address');

        $this->assertSame('address', $group->getName());
        $this->assertSame('address', $group->getLabel());
        $this->assertFalse($group->isDefault());
    }

    public function testCreateWithLabel()
    {
        $group = $this->createFactory()->create('address', ['label' => 'Address details']);

        $this->assertSame('Address details', $group->getLabel());
    }

    public function testCreateTranslatesLabel()
    {
        $translator = $this->createStub(TranslatorInterface::class);
        $translator->method('trans')->willReturnCallback(static fn (string $id) => strtoupper($id));

        $group = (new ColumnVisibilityGroupFactory($translator))->create('address', ['label' => 'foo']);

        $this->assertSame('FOO', $group->getLabel());
    }

    public function testCreateWithIsDefault()
    {
        $group = $this->createFactory()->create('address', ['is_default' => true]);

        $this->assertTrue($group->isDefault());
    }

    public function testCreateRejectsInvalidOption()
    {
        $this->expectException(UndefinedOptionsException::class);

        $this->createFactory()->create('address', ['unknown' => 'foo']);
    }

    public function testCreateRejectsInvalidLabelType()
    {
        $this->expectException(InvalidOptionsException::class);

        $this->createFactory()->create('address', ['label' => 42]);
    }

    public function testCreateRejectsInvalidIsDefaultType()
    {
        $this->expectException(InvalidOptionsException::class);

        $this->createFactory()->create('address', ['is_default' => 'yes']);
    }

    private function createFactory(): ColumnVisibilityGroupFactory
    {
        $translator = $this->createStub(TranslatorInterface::class);
        $translator->method('trans')->willReturnArgument(0);

        return new ColumnVisibilityGroupFactory($translator);
    }
}
