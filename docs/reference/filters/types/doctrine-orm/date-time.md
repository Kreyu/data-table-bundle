---
label: DateTime
order: e
tags:
  - filters
  - doctrine orm
---

# DateTime filter type

The `DateTimeFilterType` represents a filter that operates on datetime values.

+---------------------+--------------------------------------------------------------+
| Parent type         | [DoctrineOrmFilterType](doctrine-orm.md)
+---------------------+--------------------------------------------------------------+
| Class               | [:icon-mark-github: DateTimeFilterType](https://github.com/Kreyu/data-table-bundle/blob/main/src/Filter/Type/DateTimeFilterType.php)
+---------------------+--------------------------------------------------------------+
| Form Type           | [DateTimeType](https://symfony.com/doc/current/reference/forms/types/datetime.html)
+---------------------+--------------------------------------------------------------+
| Supported operators | Equals, NotEquals, GreaterThan, GreaterThanEquals, LessThan, LessThanEquals
+---------------------+--------------------------------------------------------------+
| Default operator    | Equals
+---------------------+--------------------------------------------------------------+

## Options

This filter type has no additional options.

## Inherited options

{{ option_form_type_default_value = '`\'Symfony\\Component\\Form\\Extension\\Core\\Type\\DateTimeType\'`' }}

{% capture option_empty_data_note %}
If form option `widget` equals `'choice'` or `'text'` then the normalizer changes default value to: 
```
[
    'date' => [
        'day' => '', 
        'month' => '', 
        'year' => ''
    ]
]
```
{% endcapture %}

{% capture option_form_options_notes %}
!!!
**Note**: If the `form_type` is `DateTimeType`, the normalizer adds a default `['widget' => 'single_text']`.
!!!
{% endcapture %}

{{ include '../_filter_options' }}
{{ include '_doctrine_orm_filter_options' }}