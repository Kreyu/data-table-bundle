# Twig 

## Functions

Even though the helper functions simply renders a template block of a specific part of the data table,
they are very useful because they take use the theme configured in bundle.

### `data_table`

**Usage**: `data_table(data_table_view, variables)`

Renders the HTML of a complete data table, with action bar, filtration, pagination, etc.

```twig
{{ data_table(data_table, { 'title': 'Products' }) }}
```

You will mostly use this helper for prototyping or if you use custom theme.
If you need more flexibility in rendering the data table, you should use the other helpers
to render individual parts of the data table instead.

### `data_table_table`

**Usage**: `data_table_table(data_table_view, variables)`

Renders the HTML of the data table.

### `data_table_action_bar`

**Usage**: `data_table_action_bar(data_table_view, variables)`

Renders the HTML of the data table action bar, which includes filtration, exporting and personalization features.

### `data_table_header_row`

**Usage**: `data_table_header_row(header_row_view, variables)`

Renders the header row of the data table.

### `data_table_value_row`

**Usage**: `data_table_value_row(value_row_view, variables)`

Renders the value row of the data table.

### `data_table_column_label`

**Usage**: `data_table_column_label(column_view, variables)`

Renders the label of the column. This takes care of all the label translation logic under the hood.

### `data_table_column_header`

**Usage**: `data_table_column_header(column_view, variables)`

Renders the header of the column. Internally, this does the same as `data_table_column_label()` method,
but additionally handles the sorting feature.

### `data_table_column_value`

**Usage**: `data_table_column_value(column_view, variables)`

Renders the value of the column. It handles all the required logic to extract value from the row data
based on the column configuration (e.g. to display formatted `name` of the `Project` entity).

### `data_table_filters_form`

**Usage**: `data_table_filters_form(form, variables)`

Renders the filters form. Accepts both the `FormInterface` and `FormView`.
If given value is instance of `FormInterface`, the `createView()` method will be called.

### `data_table_personalization_form`

**Usage**: `data_table_personalization_form(form, variables)`

Renders the personalization form. Accepts both the `FormInterface` and `FormView`.
If given value is instance of `FormInterface`, the `createView()` method will be called.

### `data_table_export_form`

**Usage**: `data_table_export_form(form, variables)`

Renders the export form. Accepts both the `FormInterface` and `FormView`.
If given value is instance of `FormInterface`, the `createView()` method will be called.

### `data_table_pagination`

**Usage**: `data_table_pagination(pagination_view, variables)`

Renders the pagination controls.

Additionally, accepts the data table view as a first argument.
In this case, the pagination view is extracted from the data table view "pagination" variable.

## Variables

Certain types may define even more variables, and some variables here only really apply to certain types.
To know the exact variables available for each type, check out the code of the templates used by your data table theme.

::: warning The type classes are constantly changing before the stable release!
Check source code of the type class to make sure of the variables exposed to the template.
:::

### Data table variables

The following variables are common to every data table type:

| Variable                         | Usage                                                                                                                                  |
|----------------------------------|----------------------------------------------------------------------------------------------------------------------------------------|
| `themes`                         | Themes to apply for the data table.                                                                                                    |
| `name`                           | Name of the data table.                                                                                                                |
| `title`                          | Title of the data table                                                                                                                |
| `title_translation_parameters`   | Parameters used in title translation.                                                                                                  |
| `translation_domain`             | Translation domain used in translatable strings in the data table.  If `false`, the translation is disabled.                           |
| `pagination_enabled`             | If `true`, the pagination feature is enabled.                                                                                          |
| `sorting_enabled`                | If `true`, the sorting feature is enabled.                                                                                             |
| `filtration_enabled`             | If `true`, the filtration feature is enabled.                                                                                          |
| `personalization_enabled`        | If `true`, the personalization feature is enabled.                                                                                     |
| `exporting_enabled`              | If `true`, the exporting feature is enabled.                                                                                           |
| `page_parameter_name`            | Name of the parameter that holds the current page number.                                                                              |
| `per_page_parameter_name`        | Name of the parameter that holds the pagination per page limit.                                                                        |
| `sort_parameter_name`            | Name of the parameter that holds the sorting data array (e.g. `[{sort_parameter_name}][field]`, `[{sort_parameter_name}][direction]`). |
| `filtration_parameter_name`      | Name of the parameter that holds the filtration form data.                                                                             |
| `personalization_parameter_name` | Name of the parameter that holds the personalization form data.                                                                        |
| `export_parameter_name`          | Name of the parameter that holds the export form data.                                                                                 |
| `has_active_filters`             | If at least one filter is active, this value will equal `true`.                                                                        |
| `filtration_data`                | An instance of filtration data, that contains applied filters values.                                                                  |
| `sorting_data`                   | An instance of sorting data, that contains applied sorting values.                                                                     |
| `header_row`                     | An instance of headers row view.                                                                                                       |
| `non_personalized_header_row`    | An instance of headers row view without personalization applied.                                                                       |
| `value_rows`                     | A list of instances of value rows views.                                                                                               |
| `pagination`                     | An instance of pagination.                                                                                                             |
| `actions`                        | A list of actions defined for the data table.                                                                                          |
| `filters`                        | A list of filters defined for the data table.                                                                                          |
| `exporters`                      | A list of exporters defined for the data table.                                                                                        |
| `filtration_form`                | Holds an instance of the filtration form view.                                                                                         |
| `personalization_form`           | Holds an instance of the personalization form view.                                                                                    |
| `export_form`                    | Holds an instance of the export form view.                                                                                             |

### Column header variables

The following variables are common to every column type header:

| Variable                 | Usage                                                                                                                                  |
|--------------------------|----------------------------------------------------------------------------------------------------------------------------------------|
| `name`                   | Name of the column.                                                                                                                    |
| `column`                 | An instance of column view.                                                                                                            |
| `row`                    | An instance of header row that the column belongs to.                                                                                  |
| `data_table`             | An instance of data table view.                                                                                                        |
| `label`                  | Label that will be used when rendering the column header.                                                                              |
| `translation_parameters` | Parameters used when translating the header translatable values (e.g. label).                                                          |
| `translation_domain`     | Translation domain used when translating the column translatable values. If `false`, the translation is disabled.                      |
| `sort_parameter_name`    | Name of the parameter that holds the sorting data array (e.g. `[{sort_parameter_name}][field]`, `[{sort_parameter_name}][direction]`). |
| `sorted`                 | Determines whether the column is currently being sorted.                                                                               |
| `sort_field`             | Sort field used by the sortable behavior. If `false`, the sorting is disabled for the column.                                          |
| `sort_direction`         | Direction in which the column is currently being sorted.                                                                               |
| `block_prefixes`         | A list of block prefixes respecting the type inheritance.                                                                              |
| `export`                 | An array of export options, including `label` and `translation_domain` options. Equals `false` if the column is not exportable.        |
| `attr`                   | An array of attributes used in rendering the column header.                                                                            |

### Column value variables

The following variables are common to every column type value:

| Variable                 | Usage                                                                                                                                            |
|--------------------------|--------------------------------------------------------------------------------------------------------------------------------------------------|
| `row`                    | An instance of value row that the column belongs to.                                                                                             |
| `data_table`             | An instance of data table view.                                                                                                                  |
| `data`                   | Holds the norm data of a column.                                                                                                                 |
| `value`                  | Holds the string representation of a column value.                                                                                               |
| `translation_parameters` | Parameters used when translating the translatable values.                                                                                        |
| `translation_domain`     | Translation domain used when translating the column translatable values. If `false`, the translation is disabled.                                |
| `block_prefixes`         | A list of block prefixes respecting the type inheritance.                                                                                        |
| `export`                 | An array of export options, including `data`, `value`, `label` and `translation_domain` options. Equals `false` if the column is not exportable. |
| `attr`                   | An array of attributes used in rendering the column value.                                                                                       |

### Filter variables

The following variables are common to every filter type:

| Variable                       | Usage                                                                                                             |
|--------------------------------|-------------------------------------------------------------------------------------------------------------------|
| `name`                         | Name of the filter.                                                                                               |
| `form_name`                    | Form field name of the column.                                                                                    |
| `label`                        | Label that will be used when rendering the column header.                                                         |
| `label_translation_parameters` | Parameters used when translating the `label` option.                                                              |
| `translation_domain`           | Translation domain used when translating the column translatable values. If `false`, the translation is disabled. |
| `query_path`                   | Field name used in the query (e.g. in DQL, like `product.name`)                                                   |
| `field_type`                   | FQCN of the form field type used to render the filter control.                                                    |
| `field_options`                | Array of options passed to the form type defined in the `field_type`.                                             |
| `operator_type`                | FQCN of the form field type used to render the operator control.                                                  |
| `operator_options`             | Array of options passed to the form type defined in the `operator_type`.                                          |
| `data`                         | Holds the norm data of a filter.                                                                                  |
| `value`                        | Holds the string representation of a filter value.                                                                |

### Action variables

The following variables are common to every action type:

| Variable                 | Usage                                                                                                             |
|--------------------------|-------------------------------------------------------------------------------------------------------------------|
| `name`                   | Name of the action.                                                                                               |
| `label`                  | Name of the action.                                                                                               |
| `data_table`             | An instance of data table view.                                                                                   |
| `block_prefixes`         | A list of block prefixes respecting the type inheritance.                                                         |
| `translation_domain`     | Translation domain used when translating the action translatable values. If `false`, the translation is disabled. |
| `translation_parameters` | Parameters used when translating the action translatable values (e.g. label).                                     |
| `attr`                   | An array of attributes used in rendering the action.                                                              |
| `icon_attr`              | An array of attributes used in rendering the action icon.                                                         |
| `confirmation`           | An array of action confirmation options. If `false`, action is not confirmable.                                   |

::: tip
Behind the scenes, these variables are made available to the `DataTableView`, `ColumnView` and `FilterView` objects of your data table
when the DataTable component calls `buildView()`. To see what "view" variables a particular type has,
find the source code for the used type class and look for the `buildView()` method.
:::
