# Understanding the Views API

Similar to Symfony Forms, this bundle uses a View classes, that can be used to render the data table.

```
DataTableView
├── HeaderRowView
│   ├── ColumnHeaderView
│   ├── ColumnHeaderView
│   └── ...
├── ValueRowView
│   ├── ColumnValueView
│   ├── ColumnValueView
│   └── ...
└── ValueRowView
    ├── ColumnValueView
    ├── ColumnValueView
    └── ...
```

The `DataTableView` is the root of the view hierarchy. 
It contains a collection of `HeaderRowView` and `ValueRowView` instances. 
Each of these views contains a collection of `ColumnHeaderView` and `ColumnValueView` instances.

## `HeaderRowView`

The `HeaderRowView` represents a row of headers. It contains two additional properties:

`parent`

:   Holds a reference to a `DataTableView`, which represents the whole data table.

`children`

:   Holds collection of `ColumnHeaderView` instances, so you can use it to render row columns.
    Instead of accessing the `children` property, you can iterate on the `HeaderRowView` directly:
    
    ```twig
    {% for row in header_rows %}
        {% for column in row %}
            {# ... #}
        {% endfor %}
    {% endfor %}
    ```


## `ValueRowView`

The `ValueRowView` represents a row of data. It contains five additional properties:

`parent`

:   Holds a reference to a `DataTableView`, which represents the whole data table.

`children`

:   Holds collection of `ColumnValueView` instances, so you can use it to render row columns.
    Instead of accessing the `children` property, you can iterate on the `ValueRowView` directly:
    
    ```twig
    {% for row in value_rows %}
        {% for column in row %}
            {# ... #}
        {% endfor %}
    {% endfor %}
    ```

`index`

:   Holds the index of a current row, so you can use it to render a row number:
    
    ```twig
    {% for row in value_rows %}
        {{ row.index }}
    {% endfor %}
    ```

`data`

:   Holds the data of a current row, so you can use it to access the data of a row:
    
    ```twig
    {% for row in value_rows %}
        {{ row.data.id }}
    {% endfor %}
    ```

`origin`

:   Holds a reference to a `ValueRowView` that was used as an origin of the virtual value row.
    Virtual value rows are used internally in a [CollectionColumnType](../reference/column-types/collection-column-type.md),
    where each entry contains its own `ValueRowView` with scoped data and indexes.

## `ColumnHeaderView`

The `ColumnHeaderView` represents a header of a column. It contains one additional property:

`parent`

:   Holds a reference to a `HeaderRowView`, which represents the row of data table headers.


## `ColumnValueView`

The `ColumnHeaderView` represents a header of a column. It contains three additional properties:

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
    but in some cases, it will be different. For example, if a column has a callable `formatter` specified,
    the `value` property will contain the result of the callable, while the `data` property will contain
    the original value.
