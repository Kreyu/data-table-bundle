---
order: k
---

# Internationalization

Internationalization is the cornerstone of any complex application - regardless of whether only one language is initially planned, the safe solution generally seems to be to include all labels into translation files.

The data tables are supporting the Symfony Translation component out of the box. The built-in themes contains labels translated using the `KreyuDataTable` domain. Currently, there's two locales supported by the bundle:

* Polish (pl)
* English (en)

## Changing the built-in themes locale

The bundle respects locale set in the framework. See official documentation on [how to configure the translation component](https://symfony.com/doc/current/translation.html#configuration).

## Overwriting the built-in translations

The bundle (and its themes) uses the `KreyuDataTable` translation domain. To overwrite the translations inside this domain, see official documentation on [how to overwrite a third-party bundle translations](https://symfony.com/doc/current/bundles/override.html#translations).

## Changing the data table type translation domain

There are multiple labels that can be translated — for example, column headers or filter names. 
By default, every label is translated using the `messages` domain. 
Similar to Symfony Forms component, to change the default translation domain of a data table, change the default value of `translation_domain` option in the type:

```php # src/DataTable/Type/ProductDataTableType.php
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductDataTableType extends AbstractDataTableType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'translation_domain' => 'products',
        ]);
    }
}
```

## Changing the column type translation domain

The column types are little different from the data table types — instead of `translation_domain` option, these contain two:

* `header_translation_domain` - used to translate the column headers, e.g. "Created At" into "Creation date" — by default, inherits the domain from the data table itself;
* `value_translation_domain` - used to translate the column values, for example, in case of boolean column type, it translates the "Yes" and "No" strings — it does not inherit the domain from the data table, but uses the `KreyuDataTable`.

There may be some cases, where a single column may use a different translation domain. Let's assume that the `dates` translation domain contains strings related with dates, therefore, the `createdAt` column should use the `dates` domain:

```php # src/DataTable/Type/ProductDataTableType.php
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;
use Kreyu\Bundle\DataTableBundle\Column\Type\DateTimeColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\NumberColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\TextColumnType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductDataTableType extends AbstractDataTableType
{
    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        $builder
            // This column header will use the "products"
            ->addColumn('id', NumberColumnType::class)
            // This column header will use the "products"
            ->addColumn('name', TextColumnType::class)
            // This column header will use the "dates"
            ->addColumn('createdAt', DateTimeColumnType::class, [
                'header_translation_domain' => 'dates',
            ])
        ;
    }
    
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'translation_domain' => 'products',
        ]);
    }
}
```

The internationalization chapter ends the basic usage section of the documentation. 
Now, [continue to summary](summary.md) to see what else may be important to read from now
to configure the data tables even further :rocket:
