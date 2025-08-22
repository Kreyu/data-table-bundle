# Column Visibility Groups

Column Visibility Groups allow you to organize table columns into different "views." This is useful when you have a lot of information to display in a single row and want to separate it into multiple, easily switchable groups. Users can select which group of columns to display using a dropdown in the table UI.

[[toc]]

## Basic Usage

By default, a data table has a single visibility group. You can define additional groups and assign columns to them.

```php
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;
use Kreyu\Bundle\DataTableBundle\Column\Type\NumberColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\TextColumnType;

class ExampleDataTableType extends AbstractDataTableType
{
    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        // Define visibility groups
        $builder->addColumnVisibilityGroup('default');
        $builder->addColumnVisibilityGroup('address', [
            // By default, the group label is the group name, but you can override it:
            'label' => 'Address related content',
            // By default, the first defined group is the default one, but you can override it:
            'is_default' => true,
        ]);

        // Assign groups to columns
        $builder
            ->addColumn('id', NumberColumnType::class, [
                'sort' => true,
                // Will always be displayed as it does not have any group assigned
            ])
            ->addColumn('name', TextColumnType::class, [
                'label' => 'Full name',
                'sort' => true,
            ])
            ->addColumn('streetName', TextColumnType::class, [
                'sort' => true,
                // This column will only be visible when the "address" group is selected
                'column_visibility_groups' => ['address'],
            ])
        ;
    }
}
```

## How It Works

- **Defining Groups:** Use `$builder->addColumnVisibilityGroup($name, $options)` to define one or more groups. The `label` option is used as the display name in the UI.
- **Assigning Columns:** Use the `column_visibility_groups` option in `addColumn()` to assign a column to one or more groups. If omitted or set to `null`/`[]`, the column will always be visible.
- **Switching Views:** A select dropdown appears in the table, allowing users to switch between the different column visibility groups.

## Notes

- You can define as many visibility groups as needed.
- A column can belong to multiple groups by specifying multiple group names in the `column_visibility_groups` array.
- If `column_visibility_groups` is `null` or an empty array, the column is shown in the "default" group.
- Creating a default group is optional but recommended for better user experience : it ensures that the user can go back to the base view.

## UI

When multiple visibility groups are present, a select dropdown is rendered above the table, allowing users to choose which group of columns to display.
