<?= "<?php\n" ?>

declare(strict_types=1);

namespace <?= $namespace; ?>;

<?= $use_statements; ?>

class <?= $class_name; ?> extends AbstractType
{
    public function createQuery(): ProxyQueryInterface
    {
        throw new \LogicException('Implement the "createQuery" method!');
    }

    public function configureColumns(ColumnMapperInterface $columns, array $options): void
    {
    }

    public function configureFilters(FilterMapperInterface $filters, array $options): void
    {
    }
}
