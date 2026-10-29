<script setup>
    import FilterTypeOptions from "../options/filter.md";
    import DoctrineOrmFilterTypeOptions from "../options/doctrine-orm.md";
</script>

# Doctrine ORM DateTimeFilterType

The Doctrine ORM [`DateTimeFilterType`](https://github.com/Kreyu/data-table-bundle/blob/main/src/Bridge/Doctrine/Orm/Filter/Type/DateTimeFilterType.php) represents a filter that operates on a date time values.

The active filter chip shows the date and time in the current locale, e.g. `Mar 5, 2025, 2:30:00 PM` in `en`.
Minutes and seconds follow the `with_minutes` and `with_seconds` form options.

- No `ext-intl` → `2025-03-05 14:30:00` (`Y-m-d H:i:s`)
- `input_format` set in `form_options` → that format

To change it, pass your own `active_filter_formatter`:

```php
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type\DateTimeFilterType;
use Kreyu\Bundle\DataTableBundle\Filter\FilterData;

$builder
    ->addFilter('createdAt', DateTimeFilterType::class, [
        'active_filter_formatter' => fn (FilterData $data) => $data->getValue()->format('d.m.Y H:i'),
    ])
;
```

## Options

This column type has no additional options.

## Inherited options

<FilterTypeOptions :defaults="{
    formType: 'Symfony\\Component\\Form\\Extension\\Core\\Type\\DateTimeType',
    formOptions: `['widget' => 'single_text']`
}" />

<DoctrineOrmFilterTypeOptions/>