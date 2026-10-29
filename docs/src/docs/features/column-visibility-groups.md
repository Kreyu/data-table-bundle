# Column visibility groups

**Define named column sets and let the user switch between them with one select.** Handy for wide tables: show contact columns in one group, address columns in another.

[[toc]]

## Defining groups

Set the data table `column_visibility_groups` option (name → label), then list the groups of each column:

```php src/DataTable/Type/CustomerDataTableType.php
use Kreyu\Bundle\DataTableBundle\Column\Type\TextColumnType;
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CustomerDataTableType extends AbstractDataTableType
{
    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        $builder
            ->addColumn('name', TextColumnType::class)
            ->addColumn('email', TextColumnType::class, [
                'column_visibility_groups' => ['contact'],
            ])
            ->addColumn('street', TextColumnType::class, [
                'column_visibility_groups' => ['address'],
            ])
            ->addColumn('city', TextColumnType::class, [
                'column_visibility_groups' => ['contact', 'address'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'column_visibility_groups' => [
                'contact' => 'Contact',
                'address' => 'Address',
            ],
        ]);
    }
}
```

- **The first group is the default.** `contact` here.
- **A column without groups shows in every group.** `name` here.
- **An unknown group name throws** when the data table is built.
- **Labels are translated** with the data table [`translation_domain`](../../reference/types/data-table.md#translation-domain). A `TranslatableInterface` label, e.g. `t('Address', domain: 'customer')`, uses its own domain.

The select shows up in the action bar once there are two or more groups.

::: tip Switching the group does nothing?
Ensure that the `handleRequest()` method of the data table is called:

```php
class CustomerController
{
    public function index(Request $request)
    {
        $dataTable = $this->createDataTable(...);
        $dataTable->handleRequest($request); // [!code ++]
    }
}
```
:::

## How it combines with other features

| Feature             | Behavior                                                                               |
|---------------------|----------------------------------------------------------------------------------------|
| `visible: false`    | Column stays hidden in every group.                                                    |
| Personalization     | Applied first; the group then hides columns outside it. It can't show them.            |
| Exporting           | Exports the columns of the selected group, even with "Include personalization" off.   |
| Sorting, pagination | Links and the per-page select keep the selected group.                                 |
| Filters, search     | Applying or clearing filters and searching keep the selected group.                    |
| Persistence         | Not persisted, unlike page and sort. Revisit without the query string → default group. |

The selected group lives in the URL only: `?column_visibility_group_<table name>=address`. Unknown values fall back to the default group.

## Selecting a group in PHP

```php
$dataTable->setColumnVisibilityGroup('address');
$dataTable->getColumnVisibilityGroup(); // "address", or the first group if none was set
```

## Theming

The select renders in the `column_visibility_group_selector` block. Change its look with the `select_attr` and `form_attr` variables:

```twig
{% block column_visibility_group_selector %}
    {% with { select_attr: { class: 'form-select form-select-sm' } } %}
        {{ parent() }}
    {% endwith %}
{% endblock %}
```

Changing the select submits its form through the `state` controller, enabled by default.
