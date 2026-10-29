<script setup>
    import FilterTypeOptions from "../options/filter.md";
    import DoctrineOrmFilterTypeOptions from "../options/doctrine-orm.md";
</script>

# Doctrine ORM DateRangeFilterType

The Doctrine ORM [`DateRangeFilterType`](https://github.com/Kreyu/data-table-bundle/blob/main/src/Bridge/Doctrine/Orm/Filter/Type/DateRangeFilterType.php) represents a filter that operates on a two date values that make a range.

The active filter chip shows the dates in the current locale, e.g. in `en`:

| Range | Chip |
|-------|------|
| from and to | `Mar 5, 2025 - Mar 9, 2025` |
| same day | `Mar 5, 2025` |
| from only | `After Mar 5, 2025` |
| to only | `Before Mar 9, 2025` |

No `ext-intl` → `Y-m-d`, e.g. `2025-03-05 - 2025-03-09`.

To change it, pass your own `active_filter_formatter`:

```php
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type\DateRangeFilterType;
use Kreyu\Bundle\DataTableBundle\Filter\FilterData;

$builder
    ->addFilter('createdAt', DateRangeFilterType::class, [
        'active_filter_formatter' => function (FilterData $data) {
            ['from' => $from, 'to' => $to] = $data->getValue();

            $fromText = $from?->format('d.m.Y') ?? '…';
            $toText = $to?->format('d.m.Y') ?? '…';

            return $fromText.' – '.$toText;
        },
    ])
;
```

## Options

This column type has no additional options.

## Inherited options

<FilterTypeOptions :defaults="{
    formType: 'Kreyu\\Bundle\\DataTableBundle\\Filter\\Form\\Type\\DateRangeType',
    defaultOperator: 'Kreyu\\Bundle\\DataTableBundle\\Filter\\Operator::Between'
}" />

<DoctrineOrmFilterTypeOptions/>