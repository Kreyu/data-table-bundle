# Creating column type extension

Column type extensions are incredibly powerful: they allow you to modify any existing column types across the entire system.

They have 2 main use-cases:

1. You want to add a **specific** feature to a **single** column type;
2. You want to add a **generic** feature to **several** types;

## Defining the column type extension

First, create the column type extension class extending from [AbstractTypeExtension]() (you can implement [ColumnTypeExtensionInterface]() instead if you prefer):

```php
// src/DataTable/Column/Extension/TextTypeExtension.php
namespace App\DataTable\Column\Extension;

use Kreyu\Bundle\DataTableBundle\Column\Type\TextType;
use Symfony\Component\Form\AbstractTypeExtension;

class TextTypeExtension extends AbstractTypeExtension
{
    public static function getExtendedTypes(): iterable
    {
        // return [ColumnType::class] to modify (nearly) every column in the system
        return [TextType::class];
    }
}
```
The only method you must implement is `getExtendedTypes()`, which is used to configure which field types you want to modify.

Depending on your use case, you may need to override some of the following methods:

- `buildView()`
- `configureOptions()`

For more information on what those methods do, see the [custom column field type]() article.

## Registering the extension as a service

Column type extensions must be registered as services and tagged with the `kreyu_data_table.column.type_extension` tag.
If you're using the [default services.yaml configuration](https://symfony.com/doc/current/service_container.html#service-container-services-load-example),
this is already done for you, thanks to [autoconfiguration](https://symfony.com/doc/current/service_container.html#services-autoconfigure).

Once the extension is registered, any method that you've overridden (e.g. `buildView()`) will be called whenever any column of the given type is built.

## Generic column type extensions

You can modify several column types at once by specifying their common parent ([Column Types Reference]()).
For example, several column types inherit from the TextType column type (such as `EmailType`, `LinkType`, etc.).
A column type extension applying to `TextType` (i.e. whose `getExtendedType()` method returns `TextType::class`) would apply to all of these column types.

In the same way, since all column types natively available in the bundle inherit from the `ColumnType`,
a column type extension applying to `ColumnType` would apply to all of these.
Also keep in mind that if you created (or are using) a custom column type, it's possible that it does not extend `ColumnType`,
and so your column type extension may not be applied to it.

Another option is to return multiple column types in the `getExtendedTypes()` method to extend all of them:

```php
// src/DataTable/Column/Extension/DateTimeTypeExtension.php
namespace App\Form\Extension;

use Kreyu\Bundle\DataTableBundle\Column\Extension\AbstractTypeExtension;
use Kreyu\Bundle\DataTableBundle\Column\Type\DateType;
use Kreyu\Bundle\DataTableBundle\Column\Type\DateTimeType;
use Kreyu\Bundle\DataTableBundle\Column\Type\TimeType;

class DateTimeTypeExtension extends AbstractTypeExtension
{
    // ...

    public static function getExtendedTypes(): iterable
    {
        return [DateTimeType::class, DateType::class, TimeType::class];
    }
}
```