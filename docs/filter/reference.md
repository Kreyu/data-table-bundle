# Filters reference

## Supported filters

The following filters are natively available in the bundle:

- Doctrine ORM
  - [StringFilter](doctrine/orm/string.md)
  - [NumericFilter](doctrine/orm/numeric.md)
  - [EntityFilter](doctrine/orm/entity.md)
  - [CallbackFilter](doctrine/orm/callback.md)
- Other
  - [AbstractFilter](other/abstract.md)
    
## Creating custom Doctrine ORM filter

To create a custom filter, create a class that extends `Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\AbstractFilter`.

When using default container configuration, that filter should be ready to use.  
If not, remember to tag this class as `kreyu_data_table.filter`:

```yaml
App\DataTable\Filter\MyCustomFilter:
  tags:
    - { name: 'kreyu_data_table.filter' }
```
