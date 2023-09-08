---
label: DoctrineOrm
order: z
tags:
  - filters
  - doctrine orm
---

# Doctrine ORM filter type

The `DoctrineOrmFilterType` represents a base filter used as a parent for every other Doctrine ORM filter type in the bundle.

+---------------------+--------------------------------------------------------------+
| Parent type         | [FilterType](../filter)
+---------------------+--------------------------------------------------------------+
| Class               | [:icon-mark-github: DoctrineOrmFilterType](https://github.com/Kreyu/data-table-bundle/blob/main/src/Bridge/Doctrine/Orm/Filter/Type/DoctrineOrmFilterType.php)
+---------------------+--------------------------------------------------------------+
| Form Type           | [TextType](https://symfony.com/doc/current/reference/forms/types/text.html)
+---------------------+--------------------------------------------------------------+

## Options

{{ include '_doctrine_orm_filter_options' }}

## Inherited options

{{ option_form_type_default_value = '`\'Symfony\\Component\\Form\\Extension\\Core\\Type\\DateType\'`' }}

{% capture option_empty_data_note %}
If form option `widget` equals `'choice'` or `'text'` then the normalizer changes default value to:
```
[
    'day' => '', 
    'month' => '', 
    'year' => ''
]
```
{% endcapture %}

{{ include '../_filter_options' }}
