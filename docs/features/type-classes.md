---
order: j
---

# Type classes

Multiple parts of the bundle, such as columns, filters etc. are described using the type classes.
Their purpose and method of definition is very similar to the [Symfony Form Types](https://symfony.com/doc/current/reference/forms/types.html),
which means knowing how these works really help in understanding most of the bundle.

Following parts of the bundle are defined using the type classes:

* data tables
* columns
* filters
* actions
* exporters

## Creating custom type classes

The type classes work as a blueprint that defines a configuration how its feature should work. They implement their own, feature-specific interface. 
However, it is better to extend from the abstract classes, which already implement the interface and provide some utilities.

{.compact}

| Component   | Interface                                                                                                                                    | Abstract class                                                                                                                             |
|-------------|----------------------------------------------------------------------------------------------------------------------------------------------|--------------------------------------------------------------------------------------------------------------------------------------------|
| Data tables | [:icon-mark-github: DataTableTypeInterface](https://github.com/Kreyu/data-table-bundle/blob/main/src/Type/DataTableTypeInterface.php)        | [:icon-mark-github: AbstractDataTableType](https://github.com/Kreyu/data-table-bundle/blob/main/src/Type/AbstractDataTableType.php)        |
| Columns     | [:icon-mark-github: ColumnTypeInterface](https://github.com/Kreyu/data-table-bundle/blob/main/src/Column/Type/ColumnTypeInterface.php)       | [:icon-mark-github: AbstractColumnType](https://github.com/Kreyu/data-table-bundle/blob/main/src/Column/Type/AbstractColumnType.php)       |
| Filters     | [:icon-mark-github: FilterTypeInterface](https://github.com/Kreyu/data-table-bundle/blob/main/src/Filter/Type/FilterTypeInterface.php)       | [:icon-mark-github: AbstractFilterType](https://github.com/Kreyu/data-table-bundle/blob/main/src/Filter/Type/AbstractFilterType.php)       |
| Actions     | [:icon-mark-github: ActionTypeInterface](https://github.com/Kreyu/data-table-bundle/blob/main/src/Action/Type/ActionTypeInterface.php)       | [:icon-mark-github: AbstractActionType](https://github.com/Kreyu/data-table-bundle/blob/main/src/Action/Type/AbstractActionType.php)       |
| Exporters   | [:icon-mark-github: ExporterTypeInterface](https://github.com/Kreyu/data-table-bundle/blob/main/src/Exporter/Type/ExporterTypeInterface.php) | [:icon-mark-github: AbstractExporterType](https://github.com/Kreyu/data-table-bundle/blob/main/src/Exporter/Type/AbstractExporterType.php) |

The recommended namespaces to put the types are as follows:

{.compact}

| Component   | Namespace                     |
|-------------|-------------------------------|
| Data tables | `App\DataTable\Type`          |
| Columns     | `App\DataTable\Column\Type`   |
| Filters     | `App\DataTable\Filter\Type`   |
| Actions     | `App\DataTable\Action\Type`   |
| Exporters   | `App\DataTable\Exporter\Type` |

Every type in the bundle is registered as a [tagged service](https://symfony.com/doc/current/service_container/tags.html):

{.compact}

| Component   | Type tag                         |
|-------------|----------------------------------|
| Data tables | `kreyu_data_table.type`          |
| Columns     | `kreyu_data_table.column.type`   |
| Filters     | `kreyu_data_table.filter.type`   |
| Actions     | `kreyu_data_table.action.type`   |
| Exporters   | `kreyu_data_table.exporter.type` |

!!! Note
Custom type classes are **automatically** registered as a service.
!!!

### Type inheritance

For example, let's think of a column type that represents a phone number. 
In theory, it should extend the existing text column type, only adding a phone number oriented formatting. 
In practice, the type's class **should not** extend the text type class directly:

!!!danger
This is <span style="color:#e5413e;">invalid</span> - do **NOT** use PHP class inheritance!

```php # src/DataTable/Column/Type/PhoneColumnType.php
use Kreyu\Bundle\DataTableBundle\Column\Type\TextColumnType;

class PhoneColumnType extends TextColumnType
{
}
```
!!!

Instead, it should return the fully qualified class name of the parent type in the `getParent()` method:

!!!success
This is <span style="color:#36ad99;">valid</span> - extend abstract type and return parent's class name!

```php # src/DataTable/Column/Type/PhoneColumnType.php
use Kreyu\Bundle\DataTableBundle\Column\Type\AbstractColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\TextColumnType;

class PhoneColumnType implements AbstractColumnType
{
    public function getParent(): string
    {
        return TextColumnType::class;
    }
}
```
!!!

The difference is all about the extensions. Considering the example above, while using the PHP inheritance, 
a [type extensions](#defining-the-type-extensions) defined for the text column type won't be applied to the phone column type.

### Type configuration options

Each type class contains its own set of options, that can be used to configure the type according to a specific need.
Those options can be defined in any type class `configureOptions()` method, by using the [OptionsResolver component](https://symfony.com/doc/current/components/options_resolver.html):

```php # src/DataTable/Column/Type/UserColumnType.php
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\User\UserInterface;

class UserDataTableType extends AbstractDataTableType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('display_role', false)
            ->setAllowedTypes('display_role', 'bool')
        ;
    }
}
```

Additionally, options are inherited from the type specified in the `getParent()` method:

```php # src/DataTable/Column/Type/AdminColumnType.php
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\User\UserInterface;

// This class inherits options from UserDataTableType, including "display_role"
class AdminDataTableType extends AbstractDataTableType
{
    public function getParent(): string
    {
        return UserDataTableType::class;
    }
}
```

Remember that values set in `configureOptions()` using the `->setDefault()` method are **defaults**.
This means they **still** can be provided when creating, in this example, a data table:

```php # src/Controller/UserController.php
use App\DataTable\Type\UserDataTableType;
use Kreyu\Bundle\DataTableBundle\DataTableFactoryAwareTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController
{
    use DataTableFactoryAwareTrait;

    public function index()
    {
        // This data table option "display_role" equals "false",
        // because it is not explicitly given, and falls back to default value. 
        $dataTable = $this->createDataTable(UserDataTableType::class);
        
        // This data table option "display_role" equals "true". 
        $dataTable = $this->createDataTable(UserDataTableType::class, options: [
            'display_role' => true,
        ]);
        
        // This data table option "display_role" equals "false",
        // because it is not explicitly given, and falls back to default value,
        // which is inherited from the parent type (in this case: UserDataTableType).
        $dataTable = $this->createDataTable(AdminDataTableType::class);
    }
}
```

## Defining the type extensions

The type extensions allow to easily extend existing types. Those classes contain methods similar as their corresponding feature type classes. They implement their own, feature-specific interface. For easier usage, there's also an abstract classes, which already implements the interface and provides some utilities.

{.compact}

| Component   | Interface                                                                                                                                                           | Abstract class                                                                                                                                               |
|-------------|---------------------------------------------------------------------------------------------------------------------------------------------------------------------|--------------------------------------------------------------------------------------------------------------------------------------------------------------|
| Data tables | [:icon-mark-github: DataTableTypeExtensionInterface](https://github.com/Kreyu/data-table-bundle/blob/main/src/Extension/DataTableTypeExtensionInterface.php)        | [:icon-mark-github: AbstractDataTableTypeExtension](https://github.com/Kreyu/data-table-bundle/blob/main/src/Type/AbstractDataTableExtensionType.php)        |
| Columns     | [:icon-mark-github: ColumnTypeExtensionInterface](https://github.com/Kreyu/data-table-bundle/blob/main/src/Column/Extension/ColumnTypeExtensionInterface.php)       | [:icon-mark-github: AbstractColumnTypeExtension](https://github.com/Kreyu/data-table-bundle/blob/main/src/Column/Type/AbstractColumnExtensionType.php)       |
| Filters     | [:icon-mark-github: FilterTypeExtensionInterface](https://github.com/Kreyu/data-table-bundle/blob/main/src/Filter/Extension/FilterTypeExtensionInterface.php)       | [:icon-mark-github: AbstractFilterTypeExtension](https://github.com/Kreyu/data-table-bundle/blob/main/src/Filter/Type/AbstractFilterExtensionType.php)       |
| Actions     | [:icon-mark-github: ActionTypeExtensionInterface](https://github.com/Kreyu/data-table-bundle/blob/main/src/Action/Extension/ActionTypeExtensionInterface.php)       | [:icon-mark-github: AbstractActionTypeExtension](https://github.com/Kreyu/data-table-bundle/blob/main/src/Action/Type/AbstractActionExtensionType.php)       |
| Exporters   | [:icon-mark-github: ExporterTypeExtensionInterface](https://github.com/Kreyu/data-table-bundle/blob/main/src/Exporter/Extension/ExporterTypeExtensionInterface.php) | [:icon-mark-github: AbstractExporterTypeExtension](https://github.com/Kreyu/data-table-bundle/blob/main/src/Exporter/Type/AbstractExporterExtensionType.php) |

### Setting the types to extend

Each type extension class **have to** define a list of types that it extends, using the `getExtendedTypes()` method. 
For example, if you wish to create an extension for a built-in text column type, consider following configuration:

```php # src/DataTable/Column/Extension/TextColumnTypeExtension.php
use Kreyu\Bundle\DataTableBundle\Column\Extension\AbstractColumnTypeExtension;
use Kreyu\Bundle\DataTableBundle\Column\Type\TextColumnType;

class TextColumnTypeExtension extends AbstractColumnTypeExtension
{
    public static function getExtendedTypes(): iterable
    {
        return [TextColumnType::class];
    }
}
```

To apply an extension to _every type_ in the system, use the base type of each part of the bundle. 
For example, in case of the column types:

```php # src/DataTable/Column/Extension/ColumnTypeExtension.php
use Kreyu\Bundle\DataTableBundle\Column\Type\ColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Extension\AbstractColumnTypeExtension;

class ColumnTypeExtension extends AbstractColumnTypeExtension
{
    public static function getExtendedTypes(): iterable
    {
        return [ColumnType::class];
    } 
}
```

For reference, a list of each feature base type class:

{.compact}

| Component   | Base type class                                                                                                            |
|-------------|----------------------------------------------------------------------------------------------------------------------------|
| Data tables | [:icon-mark-github: DataTableType](https://github.com/Kreyu/data-table-bundle/blob/main/src/Type/DataTableType.php)        |
| Columns     | [:icon-mark-github: ColumnType](https://github.com/Kreyu/data-table-bundle/blob/main/src/Column/Type/ColumnType.php)       |
| Filters     | [:icon-mark-github: FilterType](https://github.com/Kreyu/data-table-bundle/blob/main/src/Filter/Type/FilterType.php)       |
| Actions     | [:icon-mark-github: ActionType](https://github.com/Kreyu/data-table-bundle/blob/main/src/Action/Type/ActionType.php)       |
| Exporters   | [:icon-mark-github: ExporterType](https://github.com/Kreyu/data-table-bundle/blob/main/src/Exporter/Type/ExporterType.php) |

### Setting the extension order

Every type extension in the bundle is registered as a [tagged service](https://symfony.com/doc/current/service_container/tags.html):

{.compact}

| Component   | Service tag                                |
|-------------|--------------------------------------------|
| Data tables | `kreyu_data_table.type_extension`          |
| Columns     | `kreyu_data_table.column.type_extension`   |
| Filters     | `kreyu_data_table.filter.type_extension`   |
| Actions     | `kreyu_data_table.action.type_extension`   |
| Exporters   | `kreyu_data_table.exporter.type_extension` |

Tagged services [can be prioritized using the `priority` attribute](https://symfony.com/doc/current/service\_container/tags.html#tagged-services-with-priority) to define the order the extensions will be loaded:

```yaml # config/services.yaml
services:
    App\DataTable\Extension\ExtensionA:
        tags:
            - { name: kreyu_data_table.type_extension, priority: 1 }

    App\DataTable\Extension\ExtensionB:
        tags:
            - { name: kreyu_data_table.type_extension, priority: 2 }
```

In the example above, the `ExtensionB` will be applied before the `ExtensionA`, because it has higher priority.
Without the priority specified, the extensions would be applied in the order they are registered.

## Resolving the types

Type classes support [inheritance](#type-inheritance) and [extensions](#defining-the-type-extensions),
therefore they must be **resolved** before they can be used in the application. The resolved type classes 
has direct access to an instance of the parent type (also resolved), as well as the extensions to apply.

Each component that supports the type classes, contain its "resolved" counterpart:

{.compact}

| Component   | Resolved type class                                                                                                                        |
|-------------|--------------------------------------------------------------------------------------------------------------------------------------------|
| Data tables | [:icon-mark-github: ResolvedDataTableType](https://github.com/Kreyu/data-table-bundle/blob/main/src/Type/ResolvedDataTableType.php)        |
| Columns     | [:icon-mark-github: ResolvedColumnType](https://github.com/Kreyu/data-table-bundle/blob/main/src/Column/Type/ResolvedColumnType.php)       |
| Filters     | [:icon-mark-github: ResolvedFilterType](https://github.com/Kreyu/data-table-bundle/blob/main/src/Filter/Type/ResolvedFilterType.php)       |
| Actions     | [:icon-mark-github: ResolvedActionType](https://github.com/Kreyu/data-table-bundle/blob/main/src/Action/Type/ResolvedActionType.php)       |
| Exporters   | [:icon-mark-github: ResolvedExporterType](https://github.com/Kreyu/data-table-bundle/blob/main/src/Exporter/Type/ResolvedExporterType.php) |

Resolved type classes contain similar methods as a non-resolved types. 
To understand how resolving process works, take a look at implementation of the resolved data table type's `buildDataTable()` method:

```php # vendor/kreyu/data-table-bundle/src/Type/ResolvedDataTableType.php
public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
{
    $this->parent?->buildDataTable($builder, $options);

    $this->innerType->buildDataTable($builder, $options);

    foreach ($this->typeExtensions as $extension) {
        $extension->buildDataTable($builder, $options);
    }
}
```

Breaking it down into smaller pieces, first, the type's parent method is called:

```php #
$this->parent?->buildDataTable($builder, $options);
```

The _parent_ is an instance of already resolved type. It is based on the FQCN provided in the `getParent()` method.

Next comes the _inner type_ itself: 

```php #
$this->innerType->buildDataTable($builder, $options);
```

The _inner type_ is an instance of non-resolved type, provided with the FQCN when defining the data table.
It is very important to understand, that this method is called **after** the parent one, but **before** any extension.

Last but not least, there's the extensions:

```php #
foreach ($this->typeExtensions as $extension) {
    $extension->buildDataTable($builder, $options);
}
```

This is why [defining an order of extensions](#setting-the-extension-order) may be very important in some cases. 
Same flow applies to every resolved type class and most of its methods in the bundle.

## Accessing the type registry

The registries are the classes that stores all the types and extensions registered in the system.
Those classes are used to easily retrieve a [resolved types](#resolving-the-types), 
while only requiring a fully qualified class name of the desired type. 

Each component that supports the type classes contains its own registry:

{.compact}

| Component   | Resolved type class                                                                                                           |
|-------------|-------------------------------------------------------------------------------------------------------------------------------|
| Data tables | [:icon-mark-github: DataTableRegistry](https://github.com/Kreyu/data-table-bundle/blob/main/src/DataTableRegistry.php)        |
| Columns     | [:icon-mark-github: ColumnRegistry](https://github.com/Kreyu/data-table-bundle/blob/main/src/Column/ColumnRegistry.php)       |
| Filters     | [:icon-mark-github: FilterRegistry](https://github.com/Kreyu/data-table-bundle/blob/main/src/Filter/FilterRegistry.php)       |
| Actions     | [:icon-mark-github: ActionRegistry](https://github.com/Kreyu/data-table-bundle/blob/main/src/Action/ActionRegistry.php)       |
| Exporters   | [:icon-mark-github: ExporterRegistry](https://github.com/Kreyu/data-table-bundle/blob/main/src/Exporter/ExporterRegistry.php) |

In reality, the purpose of the registry is to:
- hold instances of the registered types and extensions; 
- create [resolved types](#resolving-the-types) using the [&nbsp;:icon-mark-github: ResolvedTypeFactoryInterface](https://github.com/Kreyu/data-table-bundle/blob/main/src/Exporter/ExporterRegistry.php);
