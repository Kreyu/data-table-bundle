<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Maker;

use Kreyu\Bundle\DataTableBundle\Column\Mapper\ColumnMapperInterface;
use Kreyu\Bundle\DataTableBundle\Filter\Mapper\FilterMapperInterface;
use Kreyu\Bundle\DataTableBundle\Query\ProxyQueryInterface;
use Kreyu\Bundle\DataTableBundle\Type\AbstractType;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Bundle\MakerBundle\Maker\AbstractMaker;
use Symfony\Bundle\MakerBundle\Str;
use Symfony\Bundle\MakerBundle\Util\UseStatementGenerator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;

class MakeDataTable extends AbstractMaker
{
    public static function getCommandName(): string
    {
        return 'make:data-table';
    }

    public static function getCommandDescription(): string
    {
        return 'Creates a new data table class';
    }

    public function configureCommand(Command $command, InputConfiguration $inputConfig): void
    {
        $command
            ->addArgument('data-table-class', InputArgument::OPTIONAL, sprintf('Choose a name for your data table class (e.g. <fg=yellow>%sType</>)', Str::asClassName(Str::getRandomTerm())))
            ->setHelp(file_get_contents(__DIR__.'/../Resources/help/MakeDataTable.txt'))
        ;
    }

    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator)
    {
        $dataTableClassNameDetails = $generator->createClassNameDetails(
            $input->getArgument('data-table-class'),
            'DataTable\\Type\\',
            'Type',
        );

        $useStatements = new UseStatementGenerator([
            AbstractType::class,
            ColumnMapperInterface::class,
            FilterMapperInterface::class,
            ProxyQueryInterface::class,
        ]);

        $generator->generateClass(
            $dataTableClassNameDetails->getFullName(),
            __DIR__.'/../Resources/skeleton/DataTableType.tpl.php',
            [
                'use_statements' => $useStatements,
            ],
        );

        $generator->writeChanges();

        $this->writeSuccessMessage($io);

        $io->text('Next: Open your new data table class and add some columns and filters!');
    }

    public function configureDependencies(DependencyBuilder $dependencies): void
    {
    }
}
