# Understanding the Views API

Similar to Symfony Forms, this bundle uses a View classes, that can be used to render the data table.


## The view hierarchy

The `DataTableView` is the root of the view hierarchy. It contains:

- a header row which contains a collection of column headers;
- a collection of value rows, where each contain a collection of column values;
- a collection of filters;
- a collection of actions;
- a pagination;

For example, a data table with three columns, two rows, three filters, 
three actions and a pagination, will have the following view hierarchy:

```
DataTableView
│
├── headerRow (HeaderRowView)
│   ├── headerRow.children.0 (ColumnHeaderView)
│   ├── headerRow.children.1 (ColumnHeaderView)
│   └── headerRow.children.2 (ColumnHeaderView)
│
├── nonPersonalizedHeaderRow (HeaderRowView)
│   ├── nonPersonalizedHeaderRow.children.0 (ColumnHeaderView)
│   ├── nonPersonalizedHeaderRow.children.1 (ColumnHeaderView)
│   └── nonPersonalizedHeaderRow.children.2 (ColumnHeaderView)
│
├── valueRows
│   ├── valueRows.0 (ValueRowView)
│   │   ├── valueRows.0.children.0 (ColumnValueView)
│   │   ├── valueRows.0.children.1 (ColumnValueView)
│   │   └── valueRows.0.children.2 (ColumnValueView)
│   └── value_rows.1 (ValueRowView)
│       ├── valueRows.1.children.0 (ColumnValueView)
│       ├── valueRows.1.children.1 (ColumnValueView)
│       └── valueRows.1.children.2 (ColumnValueView)
│
├── filters
│   ├── filters.0 (FilterView)
│   ├── filters.1 (FilterView)
│   └── filters.2 (FilterView)
│
├── actions
│   ├── actions.0 (ActionView)
│   ├── actions.1 (ActionView)
│   └── actions.2 (ActionView)
│
└── pagination (PaginationView)
```


## Reference

### Header row

The `HeaderRowView` represents a row of headers. It contains two additional properties:

!!! Note

    If the personalization is enabled, only the columns marked as visible are included in the header row.

`parent`

:   Holds a reference to a `DataTableView`, which represents the whole data table.

`children`

:   Holds collection of `ColumnHeaderView` instances, so you can use it to render row columns.
    Instead of accessing the `children` property, you can iterate on the `HeaderRowView` directly:
    
    {% raw %}
    ```twig
    {% for row in header_rows %}
        {% for column in row %}
            {# ... #}
        {% endfor %}
    {% endfor %}
    ```
    {% endraw %}

### Non personalized header row

With similar logic to the regular header row, the `nonPersonalizedHeaderRowView` represents a row of headers
with **all defined columns**, regardless of the visibility applied by the personalization.

### Value row

The `ValueRowView` represents a row of data. It contains five additional properties:

`parent`

:   Holds a reference to a `DataTableView`, which represents the whole data table.

`children`

:   Holds collection of `ColumnValueView` instances, so you can use it to render row columns.
    Instead of accessing the `children` property, you can iterate on the `ValueRowView` directly:

    {% raw %}
    ```twig
    {% for row in value_rows %}
        {% for column in row %}
            {# ... #}
        {% endfor %}
    {% endfor %}
    ```
    {% endraw %}

`index`

:   Holds the index of a current row, so you can use it to render a row number:

    {% raw %}
    ```twig
    {% for row in value_rows %}
        {{ row.index }}
    {% endfor %}
    ```
    {% endraw %}

`data`

:   Holds the data of a current row, so you can use it to access the data of a row:

    {% raw %}
    ```twig
    {% for row in value_rows %}
        {{ row.data.id }}
    {% endfor %}
    ```
    {% endraw %}

`origin`

:   Holds a reference to a `ValueRowView` that was used as an origin of the virtual value row.
    Virtual value rows are used internally in a [CollectionColumnType](../reference/column-types/collection-column-type.md),
    where each entry contains its own `ValueRowView` with scoped data and indexes.


### Column header

The `ColumnHeaderView` represents a header of a column. It contains one additional property:

`parent`

:   Holds a reference to a `HeaderRowView`, which represents the row of data table headers.


### Column value

The `ColumnHeaderView` represents a value of a column. It contains three additional properties:

`parent`

:   Holds a reference to a `ValueRowView`, which represents the row of data table headers.
    It can be used to retrieve, for example, an index or data of a row that the column belongs to.

`data`

:   Holds the norm data of a column. In most cases, this property will equal the `value` property,
    but in some cases, it will be different. For example, if a column represents an object value,
    the `value` property will contain a string representation of the object, while the `data` property
    will contain the object itself.

`value`

:   Holds the string representation of a column value. In most cases, this property will equal the `data` property,
    but in some cases, it will be different. For example, if a column has a `formatter` option specified,
    the `value` property will contain the result of the callable, while the `data` property will contain
    the original value.


### Filter

The `FilterView` represents a filter. It is used to render a list of active filters. 
It contains three additional properties:

`parent`

:   Holds a reference to a `DataTableView`, which represents the whole data table.

`data`

:   Holds the data of a filter as an instance of [:material-github: FilterData](https://github.com/Kreyu/data-table-bundle/blob/main/src/Filter/FilterData.php) class. 
    It can be used to retrieve both filter value (raw value given by the user) and selected operator. 

`value`

:   Holds the string representation of a filter value. In most cases, this property will equal the value returned
    by the `FilterData::getValue()` method, but in some cases, it will be different. For example, if a filter 
    has an `active_filter_formatter` option specified, the `value` property will contain the result of the callable.


### Action

The `ActionView` represents an action. It contains one additional property:

`parent`

:   Holds a reference to a `DataTableView`, which represents the whole data table.
    If the action is used within the [ActionsColumnType](../reference/columns/types/actions.md), then, instead of a reference to a data table,
    this property holds a reference to a `ColumnValueView`, which can be used to traverse the hierarchy further, for example, to retrieve an instance of `ValueRowView`.