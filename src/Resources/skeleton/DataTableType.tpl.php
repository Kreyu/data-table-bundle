<?php echo "<?php\n"; ?>

declare(strict_types=1);

namespace <?php echo $namespace; ?>;

<?php echo $use_statements; ?>

class <?php echo $class_name; ?> extends AbstractType
{
    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
    }
}
