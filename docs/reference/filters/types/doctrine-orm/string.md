---
label: String
order: a
tags:
  - filters
  - doctrine orm
---

# String filter type

The `StringFilterType` represents a filter that operates on string values.

+---------------------+--------------------------------------------------------------+
| Parent type         | [DoctrineOrmFilterType](doctrine-orm.md)
+---------------------+--------------------------------------------------------------+
| Class               | [:icon-mark-github: StringFilterType](https://github.com/Kreyu/data-table-bundle/blob/main/src/Filter/Type/StringFilterType.php)
+---------------------+--------------------------------------------------------------+
| Form Type           | [TextType](https://symfony.com/doc/current/reference/forms/types/text.html)
+---------------------+--------------------------------------------------------------+
| Supported operators | Equals, NotEquals, Contains, NotContains, StartsWith, EndsWith
+---------------------+--------------------------------------------------------------+
| Default operator    | Contains
+---------------------+--------------------------------------------------------------+

## Options

This filter type has no additional options.

## Inherited options

{{ include '../_filter_options' }}
{{ include '_doctrine_orm_filter_options' }}
