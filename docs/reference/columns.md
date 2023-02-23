# Columns

A data table is composed of _columns_, each of which are built with the help of a column _type_ (e.g. `NumberType`, `TextType`, etc).

## Built-in column types

The following column types are natively available in the bundle:

- Text types
    - [TextType](#texttype)
    - [NumberType](#numbertype)
    - [BooleanType](#booleantype)
    - [LinkType](#linktype)
- Special types
    - [CollectionType](#collectiontype)
    - [TemplateType](#templatetype)
    - [ActionsType](#actionstype)
- Base types
    - [ColumnType](#columntype)

{% include-markdown "columns/creating_custom_column_type.md" heading-offset=1 %}
{% include-markdown "columns/creating_column_type_extension.md" heading-offset=1 %}

## Built-in types reference

{% include-markdown "columns/types/text.md" heading-offset=2 %}
{% include-markdown "columns/types/number.md" heading-offset=2 %}
{% include-markdown "columns/types/boolean.md" heading-offset=2 %}
{% include-markdown "columns/types/datetime.md" heading-offset=2 %}
{% include-markdown "columns/types/link.md" heading-offset=2 %}
{% include-markdown "columns/types/collection.md" heading-offset=2 %}
{% include-markdown "columns/types/template.md" heading-offset=2 %}
{% include-markdown "columns/types/actions.md" heading-offset=2 %}
{% include-markdown "columns/types/column.md" heading-offset=2 %}