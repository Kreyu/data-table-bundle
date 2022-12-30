# Column types reference

## Supported column types

The following column types are natively available in the bundle:

- Text types
  - [TextType](column/column-types/text.md)
  - [NumberType](column/column-types/number.md)
  - [BooleanType](column/column-types/boolean.md)
  - [LinkType](column/column-types/link.md)
- Special types
  - [CollectionType](column/column-types/collection.md)
  - [TemplateType](column/column-types/template.md)
  - [ActionsType](column/column-types/actions.md)

## Creating custom column types

To create a custom column type, create a class that extends `Kreyu\Bundle\DataTableBundle\Column\Type\AbstractType`.  

When using default container configuration, that type should be ready to use.  
If not, remember to tag this class as `kreyu_data_table.column_type`:

```yaml
App\DataTable\Column\Type\MyCustomType:
  tags:
    - { name: 'kreyu_data_table.column_type' }
```
