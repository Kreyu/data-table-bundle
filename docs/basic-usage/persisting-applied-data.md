---
order: j
---

# Persisting applied data

In complex applications, it can be very helpful to retain data such as applied personalization, filters, applied sorting or at least the currently displayed page. The bundle comes with the persistence feature, which can be freely configured for each feature individually.

Let's focus on persisting the applied personalization data first.&#x20;

## Prerequisites

For a basic usage, we're assuming that the persistence data will be saved to a **cache**, and are saved individually per **user**. Therefore, make sure the Symfony Cache and Security components are is installed and enabled. The bundle will automatically use them for the persistence after enabling it. This topic can be quite hard to fully understand how it works at first glance, especially after using the default "magic" configuration - for extended explanation see [the persistence reference](../reference/persistence.md).

## Enabling the persistence feature

The persistence feature is disabled for each data table by default. There's multiple way to configure the persistence feature, but let's do it globally. Navigate to the package configuration file (or create one if it doesn't exist) and change it like so:

{% tabs %}
{% tab title="YAML" %}
{% code title="config/packages/kreyu_data_table.yaml" lineNumbers="true" %}
```yaml
kreyu_data_table:
  defaults:
    personalization:
      persistence_enabled: true
```
{% endcode %}
{% endtab %}

{% tab title="PHP" %}
{% code title="" lineNumbers="true" %}
```php
use Symfony\Config\KreyuDataTableConfig;

return static function (KreyuDataTableConfig $config) {
    $defaults = $config->defaults();
    $defaults->personalization()->persistenceEnabled(true);
};
```
{% endcode %}
{% endtab %}
{% endtabs %}

This configures the default option for the each data table type - which can be changed inside the data table type, inside the `configureOptions()` method:

{% code title="src/DataTable/Type/ProductDataTableType.php" lineNumbers="true" %}
```php
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductDataTableType extends AbstractDataTableType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'personalization_persistence_enabled' => true,
        ]);
    }
}
```
{% endcode %}

This, on the other hand, configures the default option for the specific data table type - which can be changed when creating the data table itself:

{% code title="src/Controller/ProductController.php" lineNumbers="true" %}
```php
use App\DataTable\Type\ProductDataTableType;
use Kreyu\Bundle\DataTableBundle\DataTableFactoryAwareTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
    use DataTableFactoryAwareTrait;
    
    public function index()
    {
        $dataTable = $this->createDataTable(
            type: ProductDataTableType::class, 
            query: $query,
            options: [
                'personalization_persistence_enabled' => true,
            ],
        );
    }
}
```
{% endcode %}

Assuming that the user is authenticated, apply the personalization data again, refresh the page and... the applied personalization is still there!

The same configuration applies to the rest of the features supporting the persistence:

* pagination (using the `pagination_persistence_*` options)
* filtration (using the `filtration_persistence_*` options)
* sorting (using the `sorting_persistence_*` options)

This basic example barely scratches the surface of the persistence feature. Is is possible to use different adapters (to, for example, save the data to database instead of cache), or different subject providers (to, for example, not rely on authenticated user, but on the request IP).&#x20;

For extended explanation see [the persistence reference](../reference/persistence.md).

There's still one thing to walk through - let's [translate the data table to multiple languages](../usage/internationalization.md).
