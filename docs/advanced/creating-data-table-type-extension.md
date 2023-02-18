# Creating data table type extension

Data table type extensions are incredibly powerful: they allow you to modify any existing data table types across the entire system.

They have 2 main use-cases:

1. You want to add a specific feature to a single data table type;
2. You want to add a generic feature to several types;

## Defining the data table type extension

First, create the data table type extension class extending from [AbstractTypeExtension](https://github.com/Kreyu/data-table-bundle/blob/main/srcsrc/Extension/AbstractTypeExtension.php) 
(you can implement [DataTableTypeExtensionInterface](https://github.com/Kreyu/data-table-bundle/blob/main/srcsrc/Extension/DataTableTypeExtensionInterface.php) instead if you prefer):

```php
// src/DataTable/Extension/LoggedUserExtension.php
namespace App\DataTable\Extension;

use App\DataTable\Type\ProductType;
use Kreyu\Bundle\DataTableBundle\Extension\AbstractTypeExtension;
use Kreyu\Bundle\DataTableBundle\Type\DataTableType;

class LoggedUserExtension extends AbstractTypeExtension
{
    public static function getExtendedTypes(): iterable
    {
        // return [DataTableType::class] to modify (nearly) every data table in the system
        return [ProductType::class];
    }
}
```

The only method you must implement is `getExtendedTypes()`, which is used to configure which types you want to modify.

Depending on your use case, you may need to override some of the following methods:

- `buildDataTable()`
- `buildView()`
- `configureOptions()`

For more information on what those methods do, see the [custom data table type]() article.

## Registering the extension as a service

Column type extensions must be registered as services and tagged with the `kreyu_data_table.type_extension` tag. 
If you're using the [default services.yaml configuration](https://symfony.com/doc/current/service_container.html#service-container-services-load-example), 
this is already done for you, thanks to [autoconfiguration](https://symfony.com/doc/current/service_container.html#services-autoconfigure).

Once the extension is registered, any method that you've overridden (e.g. `buildDataTable()`) will be called whenever _any_ data table of the given type is built.
