---
order: i
---

# Personalization

Although the product table shown in the examples is really small, imagine that it actually contains dozens of other columns - making it quickly unreadable! In addition, each user may prefer a different order of these columns. This is where the personalization functionality comes to the rescue, allowing you to freely show or hide the columns, and even determine their order.

## Enabling the personalization feature

The personalization feature is disabled for each data table by default. There's multiple way to configure the personalization feature, but let's do it globally. Navigate to the package configuration file (or create one if it doesn't exist) and change it like so:

{% tabs %}
{% tab title="YAML" %}
{% code title="config/packages/kreyu_data_table.yaml" lineNumbers="true" %}
```yaml
kreyu_data_table:
  defaults:
    personalization:
      enabled: true
```
{% endcode %}
{% endtab %}

{% tab title="PHP" %}
{% code title="config/packages/kreyu_data_table.php" lineNumbers="true" %}
```php
use Symfony\Config\KreyuDataTableConfig;

return static function (KreyuDataTableConfig $config) {
    $defaults = $config->defaults();
    $defaults->personalization()->enabled(true);
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
            'personalization_enabled' => true,
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
                'personalization_enabled' => true,
            ],
        );
    }
}
```
{% endcode %}

The personalization feature may look really handy, but refresh the page after applying the personalization - it's gone! Now imagine configuring it on every request as the user - nightmare :ghost:\
This can be solved by [enabling the persistence feature](../basic-usage/persisting-applied-data.md), which will save the personalization data (and even the applied pagination, sorting and filters if you wish!) between requests, per user.
