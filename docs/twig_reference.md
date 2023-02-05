# Twig Reference

## Functions

Even though the helper functions simply renders a template block of a specific part of the data table,
they are very useful because they take use the theme configured in bundle.

### `data_table(data_table_view, variables)`

Renders the HTML of a complete data table.

```twig
{# render the data table and disable the filtration feature #}
{{ data_table(data_table, { 'filtration_enabled': false }) }}
```

You will mostly use this helper for prototyping or if you use custom theme.
If you need more flexibility in rendering the data table, you should use the other helpers 
to render individual parts of the data table instead.

### `data_table_headers_row(headers_row_view, variables)`

Renders the headers row of the data table.

### `data_table_values_row(values_row_view, variables)`

Renders the values row of the data table.

### `data_table_column_label(column_view, variables)`

Renders the label of the column. This takes care of all the label translation logic under the hood.

### `data_table_column_header(column_view, variables)`

Renders the header of the column. Internally, this does the same as `data_table_column_label()` method,
but additionally handles the sorting feature.

### `data_table_column_value(column_view, variables)`

Renders the value of the column. It handles all the required logic to extract value from the row data
based on the column configuration (e.g. to display formatted `name` of the `Project` entity).

### `data_table_filters_form(form)`

Renders the filters form. Accepts both the `FormInterface` and `FormView`.
If given value is instance of `FormInterface`, the `createView()` method will be called.

### `data_table_personalization_form(form)`

Renders the personalization form. Accepts both the `FormInterface` and `FormView`.
If given value is instance of `FormInterface`, the `createView()` method will be called.

### `data_table_export_form(form)`

Renders the export form. Accepts both the `FormInterface` and `FormView`.
If given value is instance of `FormInterface`, the `createView()` method will be called.

### `data_table_pagination(pagination_view, variables)`

Renders the pagination controls.

## Variables

Certain types may define even more variables, and some variables here only really apply to certain types.
To know the exact variables available for each type, check out the code of the templates used by your data table theme.

### Data Table Variables

The following variables are common to every data table type. 

| Variable                         | Usage                                                                                                                                  |
|----------------------------------|----------------------------------------------------------------------------------------------------------------------------------------|
| `columns`                        | List of defined columns. If personalization is enabled, it will contain only the visible columns.                                      |
| `filters`                        | List of defined filters.                                                                                                               |
| `personalization_enabled`        | If `true`, the personalization feature is enabled.                                                                                     |
| `filtration_enabled`             | If `true`, the filtration feature is enabled.                                                                                          |
| `sorting_enabled`                | If `true`, the sorting feature is enabled.                                                                                             |
| `pagination_enabled`             | If `true`, the pagination feature is enabled.                                                                                          |
| `page_parameter_name`            | Name of the parameter that holds the current page number.                                                                              |
| `per_page_parameter_name`        | Name of the parameter that holds the pagination per page limit.                                                                        |
| `sort_parameter_name`            | Name of the parameter that holds the sorting data array (e.g. `[{sort_parameter_name}][field]`, `[{sort_parameter_name}][direction]`). |
| `filtration_parameter_name`      | Name of the parameter that holds the filtration form data.                                                                             |
| `personalization_parameter_name` | Name of the parameter that holds the personalization form data.                                                                        |
| `filtration_form`                | Holds an instance of the filtration form.                                                                                              |
| `personalization_form`           | Holds an instance of the personalization form.                                                                                         |
| `headers_row`                    | Holds an instance of the headers row view.                                                                                             |
| `values_rows`                    | List of value rows views.                                                                                                              |
| `pagination`                     | Holds an instance of the pagination view.                                                                                              |

### Column Variables

The following variables are common to every column type.

| Variable                         | Usage                                                                                                                                                                                            |
|----------------------------------|--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| `label`                          | Label that will be used when rendering the column header.                                                                                                                                        |
| `label_translation_parameters`   | Parameters used when translating the `label` option.                                                                                                                                             |
| `translation_domain`             | Translation domain used when translating the column translatable values. If `false`, the translation is disabled.                                                                                |
| `property_path`                  | Property path used by the [PropertyAccessor](https://symfony.com/doc/current/components/property_access.html) to retrieve column value of each row. If `false`, the value is retrieved manually. |
| `sort_field`                     | Sort field used by the sortable behavior. If `false`, the sorting is disabled for the column.                                                                                                    |
| `block_name`                     | See [block_name option documentation](column/types/column.md#block_name).                                                                                                                        |
| `block_prefix`                   | See [block_prefix option documentation](column/types/column.md#block_prefix).                                                                                                                    |
| `value`                          | Final value that can be rendered to the user.                                                                                                                                                    |

### Filter Variables

The following variables are common to every filter type.

| Variable                       | Usage                                                                                                             |
|--------------------------------|-------------------------------------------------------------------------------------------------------------------|
| `label`                        | Label that will be used when rendering the column header.                                                         |
| `label_translation_parameters` | Parameters used when translating the `label` option.                                                              |
| `translation_domain`           | Translation domain used when translating the column translatable values. If `false`, the translation is disabled. |
| `query_path`                   | Field name used in the query (e.g. in DQL, like `product.name`)                                                   |
| `field_type`                   | FQCN of the form field type used to render the filter control.                                                    |
| `field_options`                | Array of options passed to the form type defined in the `field_type`.                                             |
| `operator_type`                | FQCN of the form field type used to render the operator control.                                                  |
| `operator_options`             | Array of options passed to the form type defined in the `operator_type`.                                          |

> ### 💡 Important note
> Behind the scenes, these variables are made available to the `DataTableView`, `ColumnView` and `FilterView` objects of your data table
> when the DataTable component calls `buildView()`. To see what "view" variables a particular type has, 
> find the source code for the used type class and look for the `buildView()` method.

