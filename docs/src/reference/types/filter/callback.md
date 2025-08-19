<script setup>
    import FilterTypeOptions from "./options/filter.md";
</script>

# CallbackFilterType

The [`CallbackFilterType`](https://github.com/Kreyu/data-table-bundle/blob/main/src/Filter/Type/CallbackFilterType.php) represents a filter that uses a given callback as its handler.

## Options

### `callback`

- **type**: `\Closure`

Sets closure that works as a filter handler.

```php
use Kreyu\Bundle\DataTableBundle\Filter\Type\CallbackFilterType;
use Kreyu\Bundle\DataTableBundle\Filter\FilterData;
use Kreyu\Bundle\DataTableBundle\Filter\FilterInterface;
use Kreyu\Bundle\DataTableBundle\Query\ProxyQueryInterface;

$builder
    ->addFilter('name', CallbackFilterType::class, [
        'callback' => function (ProxyQueryInterface $query, FilterData $data, FilterInterface $filter): void {
            // ...
        },
    ])
;
```

## Inherited options

<FilterTypeOptions />
