# Understanding the Types API

Multiple parts of the bundle, such as columns, filters etc. are described using the type classes.
The type classes are similar to the [:material-symfony: Symfony Form Types](https://symfony.com/doc/current/reference/forms/types.html),
with small differences, making them tailored for the usage in the data tables.

Following parts of the bundle are defined the Types API:

- data tables
- columns
- filters
- actions
- exporters

## Type definition

The type classes work as a blueprint that defines a configuration how its feature should work.
They implement their own, feature-specific interface. For easier usage, there's also an abstract classes, 
which already implements the interface and provides some utilities.

| Context     | Interface                                                                                                                                   | Abstract class                                                                                                                            |
|-------------|---------------------------------------------------------------------------------------------------------------------------------------------|-------------------------------------------------------------------------------------------------------------------------------------------|
| Data tables | [:material-github: DataTableTypeInterface](https://github.com/Kreyu/data-table-bundle/blob/main/src/Type/DataTableTypeInterface.php)        | [:material-github: AbstractDataTableType](https://github.com/Kreyu/data-table-bundle/blob/main/src/Type/AbstractDataTableType.php)        |
| Columns     | [:material-github: ColumnTypeInterface](https://github.com/Kreyu/data-table-bundle/blob/main/src/Column/Type/ColumnTypeInterface.php)       | [:material-github: AbstractColumnType](https://github.com/Kreyu/data-table-bundle/blob/main/src/Column/Type/AbstractColumnType.php)       |
| Filters     | [:material-github: FilterTypeInterface](https://github.com/Kreyu/data-table-bundle/blob/main/src/Filter/Type/FilterTypeInterface.php)       | [:material-github: AbstractFilterType](https://github.com/Kreyu/data-table-bundle/blob/main/src/Filter/Type/AbstractFilterType.php)       |
| Actions     | [:material-github: ActionTypeInterface](https://github.com/Kreyu/data-table-bundle/blob/main/src/Action/Type/ActionTypeInterface.php)       | [:material-github: AbstractActionType](https://github.com/Kreyu/data-table-bundle/blob/main/src/Action/Type/AbstractActionType.php)       |
| Exporters   | [:material-github: ExporterTypeInterface](https://github.com/Kreyu/data-table-bundle/blob/main/src/Exporter/Type/ExporterTypeInterface.php) | [:material-github: AbstractExporterType](https://github.com/Kreyu/data-table-bundle/blob/main/src/Exporter/Type/AbstractExporterType.php) |

### Type options

Each type class contains its own option, that can be used to configure the type according to a specific need.
The type-specific options are defined in the `configureOptions()` method, using the [Symfony's OptionsResolver component](https://symfony.com/doc/current/components/options_resolver.html).

### Type inheritance

Similar to the form types, the type inheritance should be handled using the `getParent()` method.
For example, while a `PhoneColumnType` technically extends the `TextColumnType`, it should **NOT** extend its class:

!!! failure "Do NOT use PHP class inheritance!"

    ```php
    class PhoneColumnType extends TextColumnType
    {
    }
    ```

Instead, it should return the FQCN of the parent type in the `getParent()` method:

!!! success "Extend abstract type and use `getParent()` method instead!"

    ```php
    class PhoneColumnType implements AbstractColumnType
    {
        public function getParent(): string
        {
            return TextColumnType::class;
        }
    }
    ```

The difference is about the extensions - considering the example above, while using the PHP inheritance, 
a column type extensions that extend the `TextColumnType` won't be applied to the `PhoneColumnType`.

## Type extension definition

The type extensions allow to easily extend existing types without creating custom classes.
Those classes contain methods similar as their corresponding feature type classes.
They implement their own, feature-specific interface. For easier usage, there's also an abstract classes,
which already implements the interface and provides some utilities.

| Context     | Interface                                                                                                                                                      | Abstract class                                                                                                                                              |
|-------------|----------------------------------------------------------------------------------------------------------------------------------------------------------------|-------------------------------------------------------------------------------------------------------------------------------------------------------------|
| Data tables | [:material-github: DataTableTypeExtensionInterface](https://github.com/Kreyu/data-table-bundle/blob/main/src/Extension/Type/DataTableTypeInterface.php)        | [:material-github: AbstractDataTableTypeExtension](https://github.com/Kreyu/data-table-bundle/blob/main/src/Type/AbstractDataTableExtensionType.php)        |
| Columns     | [:material-github: ColumnTypeExtensionInterface](https://github.com/Kreyu/data-table-bundle/blob/main/src/Extension/Column/Type/ColumnTypeInterface.php)       | [:material-github: AbstractColumnTypeExtension](https://github.com/Kreyu/data-table-bundle/blob/main/src/Column/Type/AbstractColumnExtensionType.php)       |
| Filters     | [:material-github: FilterTypeExtensionInterface](https://github.com/Kreyu/data-table-bundle/blob/main/src/Extension/Filter/Type/FilterTypeInterface.php)       | [:material-github: AbstractFilterTypeExtension](https://github.com/Kreyu/data-table-bundle/blob/main/src/Filter/Type/AbstractFilterExtensionType.php)       |
| Actions     | [:material-github: ActionTypeExtensionInterface](https://github.com/Kreyu/data-table-bundle/blob/main/src/Extension/Action/Type/ActionTypeInterface.php)       | [:material-github: AbstractFilterTypeExtension](https://github.com/Kreyu/data-table-bundle/blob/main/src/Filter/Type/AbstractFilterExtensionType.php)       |
| Exporters   | [:material-github: ExporterTypeExtensionInterface](https://github.com/Kreyu/data-table-bundle/blob/main/src/Extension/Exporter/Type/ExporterTypeInterface.php) | [:material-github: AbstractExporterTypeExtension](https://github.com/Kreyu/data-table-bundle/blob/main/src/Exporter/Type/AbstractExporterExtensionType.php) |

### Type extension targets

Each type extension class defines a list of types that it extends inside its static `getExtendedTypes()` method.
For example, if you wish to create an extension that extends a `PhoneColumnType`, consider following configuration:

```php
use App\DataTable\Column\Type\PhoneColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Extension\ColumnTypeExtension;

class PhoneColumnTypeExtension extends AbstractColumnTypeExtension
{
    public static function getExtendedTypes(): iterable
    {
        return [PhoneColumnType::class];
    }
}
```

To apply extension to every type in the system, use the base type of each part of the bundle, for example, in case of the column types:

```php
use Kreyu\Bundle\DataTableBundle\Column\Type\ColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Extension\ColumnTypeExtension;

class ColumnTypeExtension extends AbstractColumnTypeExtension
{
    public static function getExtendedTypes(): iterable
    {
        return [ColumnType::class];
    } 
}
```

For reference, a list of each feature base type class:

| Context     | Base type class                                                                                                           |
|-------------|---------------------------------------------------------------------------------------------------------------------------|
| Data tables | [:material-github: DataTableType](https://github.com/Kreyu/data-table-bundle/blob/main/src/Type/DataTableType.php)        |
| Columns     | [:material-github: ColumnType](https://github.com/Kreyu/data-table-bundle/blob/main/src/Column/Type/ColumnType.php)       |
| Filters     | [:material-github: FilterType](https://github.com/Kreyu/data-table-bundle/blob/main/src/Filter/Type/FilterType.php)       |
| Actions     | [:material-github: ActionType](https://github.com/Kreyu/data-table-bundle/blob/main/src/Action/Type/ActionType.php)       |
| Exporters   | [:material-github: ExporterType](https://github.com/Kreyu/data-table-bundle/blob/main/src/Exporter/Type/ExporterType.php) |

!!! Tip

    Returned value is `iterable` type, therefore it supports returning a generator using a `yield` keyword:

    ```php
    use Kreyu\Bundle\DataTableBundle\Column\Type\ColumnType;
    use Kreyu\Bundle\DataTableBundle\Column\Extension\ColumnTypeExtension;
    
    class ColumnTypeExtension extends AbstractColumnTypeExtension
    {
        public static function getExtendedTypes(): iterable
        {
            yield ColumnType::class;
        } 
    }
    ```

## Type resolving

Because types support inheritance & extensions, they have to be **resolved** before usage.

Each part of the bundle that supports the **Types API** contains a resolved type class:

| Context     | Resolved type class                                                                                                                       |
|-------------|-------------------------------------------------------------------------------------------------------------------------------------------|
| Data tables | [:material-github: ResolvedDataTableType](https://github.com/Kreyu/data-table-bundle/blob/main/src/Type/ResolvedDataTableType.php)        |
| Columns     | [:material-github: ResolvedColumnType](https://github.com/Kreyu/data-table-bundle/blob/main/src/Column/Type/ResolvedColumnType.php)       |
| Filters     | [:material-github: ResolvedFilterType](https://github.com/Kreyu/data-table-bundle/blob/main/src/Filter/Type/ResolvedFilterType.php)       |
| Actions     | [:material-github: ResolvedActionType](https://github.com/Kreyu/data-table-bundle/blob/main/src/Action/Type/ResolvedActionType.php)       |
| Exporters   | [:material-github: ResolvedExporterType](https://github.com/Kreyu/data-table-bundle/blob/main/src/Exporter/Type/ResolvedExporterType.php) |

Resolved type classes contain same methods as a non-resolved types, and handles both inheritance & extensions.
For example, take a look at implementation of the resolved data table type's `buildDataTable()` method:

```php
public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
{
    $this->parent?->buildDataTable($builder, $options);

    $this->innerType->buildDataTable($builder, $options);

    foreach ($this->typeExtensions as $extension) {
        $extension->buildDataTable($builder, $options);
    }
}
```

First, the type's parent method is called, followed by the type itself, then comes the extensions.  
Same flow applies to every resolved type in the bundle.

## Type registry

The registry stores all the types and extensions registered in the system.
Those classes can be used to retrieve a specific type or extension using their FQCN.

Each part of the bundle that supports the **Types API** contains its own registry:

| Context     | Resolved type class                                                                                                          |
|-------------|------------------------------------------------------------------------------------------------------------------------------|
| Data tables | [:material-github: DataTableRegistry](https://github.com/Kreyu/data-table-bundle/blob/main/src/DataTableRegistry.php)        |
| Columns     | [:material-github: ColumnRegistry](https://github.com/Kreyu/data-table-bundle/blob/main/src/Column/ColumnRegistry.php)       |
| Filters     | [:material-github: FilterRegistry](https://github.com/Kreyu/data-table-bundle/blob/main/src/Filter/FilterRegistry.php)       |
| Actions     | [:material-github: ActionRegistry](https://github.com/Kreyu/data-table-bundle/blob/main/src/Action/ActionRegistry.php)       |
| Exporters   | [:material-github: ExporterRegistry](https://github.com/Kreyu/data-table-bundle/blob/main/src/Exporter/ExporterRegistry.php) |

By default, the container is passing all the types & extensions to the registry, thanks to the [:material-symfony: Tagged Services](https://symfony.com/doc/current/service_container/tags.html).
For reference, here's a list of each feature's tags:

| Context     | Type tag                         | Type extension tag                         |
|-------------|----------------------------------|--------------------------------------------|
| Data tables | `kreyu_data_table.type`          | `kreyu_data_table.type_extension`          |
| Columns     | `kreyu_data_table.column.type`   | `kreyu_data_table.column.type_extension`   |
| Filters     | `kreyu_data_table.filter.type`   | `kreyu_data_table.filter.type_extension`   |
| Actions     | `kreyu_data_table.action.type`   | `kreyu_data_table.action.type_extension`   |
| Exporters   | `kreyu_data_table.exporter.type` | `kreyu_data_table.exporter.type_extension` |

!!! Note

    Tagged services can have priority, therefore you can define the order the extensions will get loaded:

    ```yaml
    # config/services.yaml
    services:
        App\DataTable\Extension\ExtensionA:
            tags:
                - { name: kreyu_data_table.type_extension, priority: 9 }

        App\DataTable\Extension\ExtensionB:
            tags:
                - { name: kreyu_data_table.type_extension, priority: 10 }
    ```

    In the example above, the `ExtensionB` will be applied before the `Extension A`.  
    Without a priority specified, the extensions would be applied in the order they are registered.

    For more details, see [:material-symfony: Tagged Services with Priority](https://symfony.com/doc/current/service_container/tags.html#tagged-services-with-priority).
