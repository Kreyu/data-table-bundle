# Grouping

**Set `group_by` to show a header row above each group of rows** that share a value.

[[toc]]

## Grouping by a property path

```php
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductDataTableType extends AbstractDataTableType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('group_by', 'category.name');
    }
}
```

The path is used twice:

- reads the group value from each row, like a column's `property_path`
- sorts the query by it first, so each group stays in one piece

Sorting by a column still works inside each group. Sorting by the group path itself flips the group order.

::: warning The path must also be valid in your query.
Doctrine ORM: `category.name` needs `->leftJoin('product.category', 'category')` in the query builder.
Rows without a category → use the null-safe path `category?.name` (Symfony 6.2+). Plain `category.name` throws on them.
:::

::: warning Grouping sorts through the sorting feature.
With `sorting_enabled: false`, sort the query by the group path yourself.
:::

## Grouping by a callable

Pass a callable to compute the group value. It gets the row data:

```php
$resolver->setDefault('group_by', static fn (Product $product): string => $product->getCreatedAt()->format('Y-m'));
```

::: warning A callable doesn't sort the query.
Sort it so equal values sit next to each other, e.g. with [default sorting](sorting.md#default-sorting).
:::

## How rows are grouped

- **A new header starts whenever the value changes** from the previous row.
- Values are compared with `===`. Two `DateTime` objects with the same date and time are one group, even as separate objects.
- Other objects are one group only when they're the same instance. Grouping by a value object (UUID, money)? Return a scalar from a `group_by` callable.
- Each page starts with a header, even when its group began on the previous page.
- Exports and the [profiler](profiler.md) list only the data rows.

## Customizing the header row

The built-in themes print the group value as text. **Group by a date or an object → override the block:**

```twig
{% block table_body_row_group_header %}
    <tr>
        <th colspan="{{ column_count }}">{{ value_row.vars.group_value|date('F Y') }}</th>
    </tr>
{% endblock %}
```

| Variable                           | Meaning                                       |
|------------------------------------|-----------------------------------------------|
| `value_row.vars.group_value`       | Group value                                   |
| `value_row.vars.row_type`          | `group_header` (data rows: `value`)           |
| `column_count`                     | Visible columns, for `colspan`                |
| `table_body_row_group_header_attr` | Attributes of the `<th>`                      |

Bootstrap 5 adds `class="table-light"` to the `<th>`. Change it without overriding the block:

```twig
{{ data_table(products, { table_body_row_group_header_attr: { class: 'table-secondary' } }) }}
```

::: warning Overriding `table_body_row_results`? Render the header rows too.
They come in `value_rows` with `row_type == 'group_header'` and no cells:

```twig
{% for value_row in value_rows %}
    {% if value_row.vars.row_type == 'group_header' %}
        {{ block('table_body_row_group_header', theme) }}
    {% else %}
        {# your data row #}
    {% endif %}
{% endfor %}
```
:::

See [Theming](theming.md) for where to put block overrides.
