<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Unit\Bridge\Doctrine\Orm\Filter\Type;

use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\Mapping\ClassMetadata;
use Doctrine\Persistence\ObjectManager;
use Kreyu\Bundle\DataTableBundle\Filter\Extension\PreloadedFilterExtension;
use Kreyu\Bundle\DataTableBundle\Filter\Operator;
use Kreyu\Bundle\DataTableBundle\Test\Filter\FilterTypeTestCase;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type\EntityFilterType;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class EntityFilterTypeTest extends FilterTypeTestCase
{
    private MockObject&ClassMetadata $classMetadata;
    private MockObject&ManagerRegistry $managerRegistry;

    protected function setUp(): void
    {
        $this->classMetadata = $this->createMock(ClassMetadata::class);

        $manager = $this->createMock(ObjectManager::class);
        $manager->method('getClassMetadata')->willReturn($this->classMetadata);

        $this->managerRegistry = $this->createMock(ManagerRegistry::class);
        $this->managerRegistry->method('getManagerForClass')->willReturn($manager);

        parent::setUp();
    }

    protected function getTestedType(): string
    {
        return EntityFilterType::class;
    }

    protected function getSupportedOperators(): array
    {
        return [
            Operator::Equals,
            Operator::NotEquals,
            Operator::In,
            Operator::NotIn,
        ];
    }

    protected function getDefaultFormType(): string
    {
        return EntityType::class;
    }

    protected function getExtensions(): array
    {
        $type = new EntityFilterType($this->managerRegistry);

        return [
            new PreloadedFilterExtension([$type], []),
        ];
    }

    public function testItShouldNotModifyFormOptionsWhenFormTypeIsNotEntityType()
    {
        $formOptions = ['trim' => false];

        $filter = $this->createFilter(['form_type' => TextType::class, 'form_options' => $formOptions]);

        $this->assertEquals($formOptions, $filter->getConfig()->getOption('form_options'));
    }

    public function testItShouldNotModifyFormOptionsWhenClassFormOptionIsNotGiven()
    {
        $formOptions = ['trim' => false];

        $filter = $this->createFilter(['form_options' => $formOptions]);

        $this->assertEquals($formOptions, $filter->getConfig()->getOption('form_options'));
    }

    public function testItShouldAddChoiceValueFormOptionWhenFormTypeIsEntityTypeAndClassFormOptionIsGiven(): void
    {
        $this->classMetadata->method('getIdentifier')->willReturn(['id']);

        $filter = $this->createFilter([
            'form_options' => $formOptions = [
                'class' => 'App\\Entity\\Product',
            ],
        ]);

        $expectedFormOptions = $formOptions + ['choice_value' => 'id'];

        $this->assertEquals($expectedFormOptions, $filter->getConfig()->getOption('form_options'));
    }

    public function testItShouldNotAddChoiceValueFormOptionWhenClassMetadataReturnsMultipleIdentifiers(): void
    {
        $this->classMetadata->method('getIdentifier')->willReturn(['id', 'uuid']);

        $filter = $this->createFilter([
            'form_options' => $formOptions = [
                'class' => 'App\\Entity\\Product',
            ],
        ]);

        $this->assertEquals($formOptions, $filter->getConfig()->getOption('form_options'));
    }

    public function testItShouldNotOverwriteChoiceValueFormOptionIfAlreadyGiven(): void
    {
        $this->classMetadata->method('getIdentifier')->willReturn(['id']);

        $filter = $this->createFilter([
            'form_options' => $formOptions = [
                'class' => 'App\\Entity\\Product',
                'choice_value' => 'uuid',
            ],
        ]);

        $this->assertEquals($formOptions, $filter->getConfig()->getOption('form_options'));
    }
}
