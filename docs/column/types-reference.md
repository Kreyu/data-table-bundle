# Column types reference

## Supported column types

The following column types are natively available in the bundle:

- Text types
  - [TextType](types/text.md)
  - [NumberType](types/number.md)
  - [BooleanType](types/boolean.md)
  - [LinkType](types/link.md)
- Special types
  - [CollectionType](types/collection.md)
  - [TemplateType](types/template.md)
  - [ActionsType](types/actions.md)
- Other
  - [AbstractType](types/abstract.md) 

## Creating custom column types

To create a custom column type, create a class that extends `Kreyu\Bundle\DataTableBundle\Column\Type\AbstractType`.  

When using default container configuration, that type should be ready to use.  
If not, remember to tag this class as `kreyu_data_table.column_type`:

```yaml
App\DataTable\Column\Type\MyCustomType:
  tags:
    - { name: 'kreyu_data_table.column_type' }
```
