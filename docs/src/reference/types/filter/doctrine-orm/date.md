<script setup>
    import FilterTypeOptions from "../options/filter.md";
    import DoctrineOrmFilterTypeOptions from "../options/doctrine-orm.md";
</script>

# Doctrine ORM DateFilterType

The Doctrine ORM [`DateFilterType`](https://github.com/Kreyu/data-table-bundle/blob/main/src/Bridge/Doctrine/Orm/Filter/Type/DateFilterType.php) represents a filter that operates on a date (without time) values.

The active filter chip shows the date in the current locale, e.g. `Mar 5, 2025` in `en`.

- No `ext-intl` → `2025-03-05` (`Y-m-d`)
- `input_format` set in `form_options` → that format

To change it, pass your own `active_filter_formatter`:

```php
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type\DateFilterType;
use Kreyu\Bundle\DataTableBundle\Filter\FilterData;

$builder
    ->addFilter('createdAt', DateFilterType::class, [
        'active_filter_formatter' => fn (FilterData $data) => $data->getValue()->format('d.m.Y'),
    ])
;
```

## Options

This column type has no additional options.

## Inherited options

<FilterTypeOptions :defaults="{
    formType: 'Symfony\\Component\\Form\\Extension\\Core\\Type\\DateType',
    formOptions: `['widget' => 'single_text']`
}" />

<DoctrineOrmFilterTypeOptions/>