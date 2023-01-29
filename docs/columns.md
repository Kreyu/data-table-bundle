# Columns

A data table is composed of _columns_, each of which are built with the help of a column _type_ (e.g. `NumberType`, `TextType`, etc).

## Supported column types

The following column types are natively available in the bundle:

- Text columns
	- [TextType](../docs/column/types/text.md)
	- [NumberType](../docs/column/types/number.md)
	- [BooleanType](../docs/column/types/boolean.md)
	- [LinkType](../docs/column/types/link.md)
- Special columns
	- [CollectionType](../docs/column/types/collection.md)
	- [TemplateType](../docs/column/types/template.md)
	- [ActionsType](../docs/column/types/actions.md)
- Base columns
	- [ColumnType](../docs/column/types/column.md)

## Creating custom column type

To create a custom column type, create a class that extends `Kreyu\Bundle\DataTableBundle\Column\Type\AbstractType`:

```php
class CustomType extends \Kreyu\Bundle\DataTableBundle\Column\Type\AbstractType
{
	public function configureOptions(\Symfony\Component\OptionsResolver\OptionsResolver $resolver) : void{
	  parent::configureOptions($resolver);
	}
}
```

When using default container configuration, that type should be ready to use.  
If not, remember to tag this class as `kreyu_data_table.column_type`:

```yaml
App\DataTable\Column\Type\MyCustomType:
  tags:
    - { name: 'kreyu_data_table.column_type' }
```

## Working with the data table builder

The data table builder contains methods that are used to describe the data table columns.

### Adding a column

To add a column to the data table, use the `addColumn()` method of the data table builder:

```php
use Kreyu\Bundle\DataTableBundle\Column\Type\TextType;
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;

/** @var DataTableBuilderInterface $builder */
$builder->addColumn('name', TextType::class);
```

A column must have a unique name within the data table. Otherwise, the existing column is overwritten.



### Removing a column

To remove a column, use the `removeColumn()` method of the data table builder:

```php
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;

/** @var DataTableBuilderInterface $builder */
$builder->removeColumn('name');
```

Any attempt of removal a non-existent column will result in a success.

### Retrieving a list of columns

To list currently added columns, use the `getColumns()` method of the data table builder:

```php
use Kreyu\Bundle\DataTableBundle\Column\ColumnInterface;
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;

/** 
 * @var DataTableBuilderInterface $builder
 * @var array<string, ColumnInterface> $columns  
 */
$columns = $builder->getColumns();
```

Returned array is indexed by the column name.

### Retrieving a column factory

To retrieve a factory used to create column objects (e.g. in the `addColumn()` method), use the `getColumnFactory` method of the data table builder: 

```php
use Kreyu\Bundle\DataTableBundle\Column\ColumnInterface;
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Column\ColumnFactoryInterface;

/** 
 * @var DataTableBuilderInterface $builder
 * @var ColumnFactoryInterface $columnFactory  
 */
$columnFactory = $builder->getColumnFactory(); 
```

### Changing a column factory

To change a factory used to create column objects (e.g. in the `addColumn()` method), use the `setColumnFactory` method of the data table builder:

```php
use Kreyu\Bundle\DataTableBundle\Column\ColumnInterface;
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Column\ColumnFactoryInterface;

/** 
 * @var DataTableBuilderInterface $builder
 * @var ColumnFactoryInterface $columnFactory  
 */
$builder->setColumnFactory($columnFactory); 
```
