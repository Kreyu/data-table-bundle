---
label: Numeric
order: b
---

# Numeric filter type

The `NumericFilterType` represents a filter that operates on numeric values.

+---------------------+--------------------------------------------------------------+
| Parent type         | [FilterType](../filter)
+---------------------+--------------------------------------------------------------+
| Class               | [:icon-mark-github: NumericFilterType](https://github.com/Kreyu/data-table-bundle/blob/main/src/Filter/Type/NumericFilterType.php)
+---------------------+--------------------------------------------------------------+
| Form Type           | [TextType](https://symfony.com/doc/current/reference/forms/types/text.html)
+---------------------+--------------------------------------------------------------+
| Supported operators | Equals, NotEquals, GreaterThan, GreaterThanEquals, LessThan, LessThanEquals
+---------------------+--------------------------------------------------------------+

## Options

This filter type has no additional options.

## Inherited options

{{ option_form_type_default_value = '`\'Symfony\\Component\\Form\\Extension\\Core\\Type\\NumberType\'`' }}

{{ include '_doctrine_orm_filter_options' }}
