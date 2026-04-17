# Column Visibility Groups

Column visibility groups let you define several **predefined views** over the same data table.
Each group is a curated subset of columns, and the user switches between them via a dropdown rendered above the table.
Typical use cases: showing "general" vs "details" views on a wide table, or giving different roles their own column sets.

::: tip Visibility groups vs personalization
Visibility groups are for **curated, designer-controlled** column sets defined in code. The
[personalization feature](personalization.md) is for **user-controlled** column selection saved per
user. The two features compose (see [Interactions](#interactions-with-other-features)) but serve
different intents.
:::

[[toc]]

## Basic Usage

Define groups with `$builder->addColumnVisibilityGroup()`, then opt columns into one or more groups
with the `column_visibility_groups` column option.

```php
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;
use Kreyu\Bundle\DataTableBundle\Column\Type\NumberColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\TextColumnType;

class CustomerDataTableType extends AbstractDataTableType
{
    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        $builder
            ->addColumnVisibilityGroup('general', ['is_default' => true])
            ->addColumnVisibilityGroup('address', ['label' => 'Address details'])
        ;

        $builder
            // No "column_visibility_groups" option: always visible, in every group.
            ->addColumn('id', NumberColumnType::class)
            ->addColumn('name', TextColumnType::class)

            // Visible only when the "general" group is selected.
            ->addColumn('email', TextColumnType::class, [
                'column_visibility_groups' => ['general'],
            ])

            // Visible only when the "address" group is selected.
            ->addColumn('streetName', TextColumnType::class, [
                'column_visibility_groups' => ['address'],
            ])
        ;
    }
}
```

As soon as a data table has at least one visibility group, a `<select>` is rendered above the table
so the user can switch between groups.

::: tip Define at least two groups
A single group still produces a one-entry `<select>` with nothing to switch to. Define at least two
groups — or don't define any and let the default "all columns visible" behavior do the job.
:::

## Defining groups

Each call to `addColumnVisibilityGroup($name, $options)` adds one group. The `$name` must be unique
within the data table and is used as the value in the URL query parameter.

The following options are accepted:

| Option       | Type             | Default  | Description                                                                  |
|--------------|------------------|----------|------------------------------------------------------------------------------|
| `label`      | `null \| string` | `null`   | Display name used in the dropdown. Falls back to the group `$name` if null.  |
| `is_default` | `bool`           | `false`  | Marks this group as the default selection when no URL parameter is present.  |

::: warning Only one default group
At most **one** group may be marked as `is_default: true`. Setting two or more throws an
`InvalidArgumentException` the first time the data table is built (via `getDataTable()` or during
rendering) with a message listing the offending groups.
:::

::: tip Fallback when no default is set
If no group is marked as default, the **first group defined** via `addColumnVisibilityGroup()` is
used as the default.
:::

## Assigning columns to groups

Use the `column_visibility_groups` option on `addColumn()`:

```php
$builder
    // Belongs to no group — always visible.
    ->addColumn('id', NumberColumnType::class)

    // Shorthand: a single group name as a string.
    ->addColumn('email', TextColumnType::class, [
        'column_visibility_groups' => 'general',
    ])

    // Multiple groups: the column appears in any of them.
    ->addColumn('phone', TextColumnType::class, [
        'column_visibility_groups' => ['general', 'contact'],
    ])
;
```

The option accepts `null`, a `string`, or an `array` of strings. `null` or `[]` means the column
does not belong to any group and is visible regardless of the selected group.

::: warning Unknown group names throw
If a column references a group name that was never defined with `addColumnVisibilityGroup()`,
`getDataTable()` throws an `InvalidArgumentException` listing the offending column, the unknown
group, and the groups that are actually defined. This catches typos at build time rather than
producing a silently hidden column.
:::

## Default group resolution

When the request arrives, the selected group is resolved in this order:

1. The value of the URL query parameter if it matches a defined group name
2. Otherwise the group marked `is_default: true`
3. Otherwise the first group defined via `addColumnVisibilityGroup()`

An invalid group name in the query parameter is treated as if no parameter was provided
(it falls through to step 2, then step 3).

## URL query parameter

The selected group is persisted in the URL so it survives refreshes and can be shared.
The parameter is `column_visibility_group` optionally suffixed with an underscore and the data table
name:

```
?column_visibility_group_<data_table_name>=address
```

The data table name comes from `$dataTable->getConfig()->getName()`. By default it is derived from
the data table type's class name, stripped of its `DataTableType` suffix and converted to snake
case — `CustomerDataTableType` becomes `customer`, so the parameter is `column_visibility_group_customer`.
If the data table has no name, the parameter falls back to plain `column_visibility_group`.

You can always read the exact parameter name from
`$dataTable->getConfig()->getColumnVisibilityGroupParameterName()` when you need to build URLs yourself.

## Translating group labels

Group labels are translated at build time using the default Symfony translator. If `label` is `null`,
the group `$name` is passed to the translator instead — so a technical name like `address` ends up
rendered in the UI if no translation exists for it.

```php
$builder->addColumnVisibilityGroup('address', [
    'label' => 'customer.visibility.address',
]);
```

::: warning Translation domain
The label is translated using the **default catalog** (typically `messages`), not the
`translation_domain` configured on the data table. Make sure your translation keys live in that
catalog, or pass a pre-translated string directly.
:::

## Interactions with other features

### Personalization

Personalization and visibility groups compose asymmetrically:

- If personalization **hides** a column, it stays hidden regardless of the selected group.
  Manual personalization wins.
- If personalization **shows** a column that the selected group excludes, the group still hides it.
  The group wins.

In other words, a column is visible only if both personalization and the selected group agree it
should be.

### Sorting

A column that is hidden — whether by personalization or because it does not belong to the selected
group — is removed from the header row, so its sort link is not rendered. Existing sort state on
a hidden column has no visible effect until the column becomes visible again.

### Filtering

::: warning Filters on hidden columns stay active
Switching to a group that does not contain a previously filtered column does **not** clear the
filter. The underlying query is still filtered, but the user has no visual indication of which
column is filtering the results. If this matters for your UX, clear filters explicitly when
switching groups.
:::

### Pagination and sorting state

When the user changes the selected group, the current `page`, `limit`, `sort`, and filter values
are re-submitted as hidden form inputs. The user stays on the same page with the same sort and
filters applied — only the visible columns change.

## Customizing the UI

The dropdown is rendered by the `column_visibility_group_selector` block defined in
`@KreyuDataTable/themes/base.html.twig`. Override it in your own theme to change the markup or
styling:

```twig
{# templates/data_table/my_theme.html.twig #}
{% extends '@KreyuDataTable/themes/bootstrap_5.html.twig' %}

{% block column_visibility_group_selector %}
    {% if data_table.columnVisibilityGroups is not empty %}
        <div class="my-custom-wrapper">
            {{ parent() }}
        </div>
    {% endif %}
{% endblock %}
```

The block receives:

- `data_table.columnVisibilityGroups` — an array of `ColumnVisibilityGroupView` with `name`,
  `label`, `isDefault`, and `isSelected` public properties
- `data_table.vars.column_visibility_group_parameter_name` — the query parameter name
- `data_table.vars.url_query_parameters` — the current URL state (shared with pagination, sorting,
  and filtering); re-emitted as hidden inputs to preserve page/sort/filter on group change

A companion block, `column_visibility_group_selector_hidden_input`, recursively renders the
hidden inputs for nested query parameters (filters in particular).
