---
label: Boolean
order: c
---

# Boolean filter type

The `BooleanFilterType` represents a filter that operates on boolean values.

+---------------------+--------------------------------------------------------------+
| Parent type         | [DoctrineOrmFilterType](doctrine-orm.md)
+---------------------+--------------------------------------------------------------+
| Class               | [:icon-mark-github: BooleanFilterType](https://github.com/Kreyu/data-table-bundle/blob/main/src/Filter/Type/BooleanFilterType.php)
+---------------------+--------------------------------------------------------------+
| Form Type           | [ChoiceType](https://symfony.com/doc/current/reference/forms/types/choice.html)
+---------------------+--------------------------------------------------------------+
| Supported operators | Equals, NotEquals
+---------------------+--------------------------------------------------------------+

## Options

This filter type has no additional options.

## Inherited options

{{ option_form_type_default_value = '`\'Symfony\\Component\\Form\\Extension\\Core\\Type\\ChoiceType\'`' }}

{{ include '../_filter_options' }}
{{ include '_doctrine_orm_filter_options' }}
