### `trim`

- **type**: `bool`
- **default**: `false`

Determines whether the `TRIM()` function should be applied on the expression. Uses the [`TrimExpressionTransformer`](https://github.com/Kreyu/data-table-bundle/blob/main/src/Bridge/Doctrine/Orm/Filter/ExpressionTransformer/TrimExpressionTransformer.php) transformer.

### `lower`

- **type**: `bool`
- **default**: `false`

Determines whether the `LOWER()` function should be applied on the expression for case-insensitive filtering. Uses the [`LowerExpressionTransformer`](https://github.com/Kreyu/data-table-bundle/blob/main/src/Bridge/Doctrine/Orm/Filter/ExpressionTransformer/LowerExpressionTransformer.php) transformer.

### `upper`

- **type**: `bool`
- **default**: `false`

Determines whether the `UPPER()` function should be applied on the expression for case-insensitive filtering. Uses the [`UpperExpressionTransformer`](https://github.com/Kreyu/data-table-bundle/blob/main/src/Bridge/Doctrine/Orm/Filter/ExpressionTransformer/UpperExpressionTransformer.php) transformer.

### `expression_transformers`

- **type**: [`ExpressionTransformerInterface[]`](https://github.com/Kreyu/data-table-bundle/blob/main/src/Bridge/Doctrine/Orm/Filter/ExpressionTransformer/ExpressionTransformerInterface.php)
- **default**: `[]`

Defines expression transformers to apply on the expression.

```php
use App\DataTable\Filter\ExpressionTransformer\UnaccentExpressionTransformer;
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\ExpressionTransformer\LowerExpressionTransformer;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\ExpressionTransformer\TrimExpressionTransformer;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type\TextFilterType;

class ProductDataTableType extends AbstractDataTableType
{
    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        $builder
            ->addFilter('name', TextFilterType::class, [
                'expression_transformers' => [
                    new LowerExpressionTransformer(),
                    new TrimExpressionTransformer(),
                ],
            ])
        ;
    }
}
```

For more information about expression transformers, [read here](../../../../docs/integrations/doctrine-orm/expression-transformers.md).
