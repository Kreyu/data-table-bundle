<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Maker;

use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Exception\RuntimeException;
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;
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
use Symfony\Component\OptionsResolver\OptionsResolver;

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
        $helpPath = __DIR__.'/../Resources/help/MakeDataTable.txt';
        $help = file_get_contents($helpPath);

        if ($help === false) {
            throw new RuntimeException(sprintf('Unable to read the help file "%s".', $helpPath));
        }

        $command
            ->addArgument(
                'data-table-class',
                InputArgument::OPTIONAL,
                sprintf(
                    'Choose a name for your data table class (e.g. <fg=yellow>%sDataTableType</>)',
                    Str::asClassName(Str::getRandomTerm()),
                ),
            )
            ->setHelp($help)
        ;
    }

    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator): void
    {
        /** @var string $dataTableClass */
        $dataTableClass = $input->getArgument('data-table-class');

        $dataTableClassNameDetails = $generator->createClassNameDetails(
            $dataTableClass,
            'DataTable\\Type\\',
            'DataTableType',
        );

        $useStatements = new UseStatementGenerator([
            AbstractDataTableType::class,
            DataTableBuilderInterface::class,
            OptionsResolver::class,
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

    public function configureDependencies(DependencyBuilder $dependencies): void {}
}
