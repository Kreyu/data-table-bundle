<?php echo "<?php\n"; ?>

declare(strict_types=1);

namespace <?php echo $namespace; ?>;

<?php echo $use_statements; ?>

class <?php echo $class_name; ?> extends AbstractType
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
