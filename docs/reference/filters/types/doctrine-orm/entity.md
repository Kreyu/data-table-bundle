---
label: Entity
visibility: hidden
order: f
---

# Entity filter type

The `EntityFilterType` represents a filter that operates on identifier values.

Displayed as a selector, allows the user to select a specific entity loaded from the database, to query by its identifier.

+---------------------+--------------------------------------------------------------+
| Parent type         | [FilterType](../../filter)
+---------------------+--------------------------------------------------------------+
| Class               | [:icon-mark-github: EntityFilterType](https://github.com/Kreyu/data-table-bundle/blob/main/src/Filter/Type/EntityFilterType.php)
+---------------------+--------------------------------------------------------------+
| Form Type           | [EntityType](https://symfony.com/doc/current/reference/forms/types/entity.html)
+---------------------+--------------------------------------------------------------+
| Supported operators | EQUALS, NOT_EQUALS, CONTAINS, NOT_CONTAINS
+---------------------+--------------------------------------------------------------+

## Options

This filter type has no additional options.

## Inherited options

{{ include '_filter_options' }}
