# Expression transformers

Expression transformers provide a way to extend Doctrine DQL expressions before they are executed by a filter handler.

[[toc]]

## Built-in expression transformers

- `TrimExpressionTransformer` - wraps the expression in the `TRIM()` function
- `LowerExpressionTransformer` - wraps the expression in the `LOWER()` function
- `UpperExpressionTransformer` - wraps the expression in the `UPPER()` function
- `CallbackExpressionTransformer` - allows transforming the expression using the callback function

The expression transformers can be passed using the `expression_transformers` option:

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

<div class="tip custom-block" style="padding-top: 8px;">

The transformers are called in the same order as they are passed.

</div>

For easier usage, some of the built-in transformers can be enabled using the `trim`, `lower` and `upper` filter options:

```php
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type\TextFilterType;

class ProductDataTableType extends AbstractDataTableType
{
    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        $builder
            ->addFilter('name', TextFilterType::class, [
                'trim' => true,
                'lower' => true,
                'upper' => true,
            ])
        ;
    }
}
```

<div class="warning custom-block" style="padding-top: 8px;">

When using the `trim`, `lower` or `upper` options, their transformers are called **before** the `expression_transformers` ones.

</div>

## Creating a custom expression transformer

To create a custom expression transformer, create a new class that implements `ExpressionTransformerInterface`:

```php
namespace App\DataTable\Filter\ExpressionTransformer;

use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\Query\Expr\Comparison;
use Kreyu\Bundle\DataTableBundle\Exception\UnexpectedTypeException;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\ExpressionTransformer\ExpressionTransformerInterface;

class UnaccentExpressionTransformer implements ExpressionTransformerInterface
{
    public function transform(mixed $expression): mixed
    {
        if (!$expression instanceof Comparison) {
            throw new UnexpectedTypeException($expression, Comparison::class);
        }

        $leftExpr = sprintf('UNACCENT(%s)', (string) $expression->getLeftExpr());
        $rightExpr = sprintf('UNACCENT(%s)', (string) $expression->getRightExpr());
        
        // or use expression API:
        //
        // $leftExpr = new Expr\Func('UNACCENT', $expression->getLeftExpr());
        // $rightExpr = new Expr\Func('UNACCENT', $expression->getRightExpr());

        return new Comparison($leftExpr, $expression->getOperator(), $rightExpr);
    }
}
```

If you're sure that the expression transformer requires the expression to be a comparison (it will be in most cases),
you can extend the `AbstractComparisonExpressionTransformer` class which simplifies the definition:

```php
namespace App\DataTable\Filter\ExpressionTransformer;

use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\ExpressionTransformer\AbstractComparisonExpressionTransformer;

class UnaccentExpressionTransformer extends AbstractComparisonExpressionTransformer
{
    protected function transformLeftExpr(mixed $leftExpr): mixed
    {
        return sprintf('UNACCENT(%s)', (string) $leftExpr);
        
        // or use expression API: 
        // 
        // return new Expr\Func('UNACCENT', $leftExpr);
    }

    protected function transformRightExpr(mixed $rightExpr): mixed
    {
        return sprintf('UNACCENT(%s)', (string) $rightExpr);
        
        // or use expression API: 
        //
        // return new Expr\Func('UNACCENT', $rightExpr);
    }
}
```

The `AbstractComparisonExpressionTransformer` accepts two boolean arguments:

- `transformLeftExpr` - defaults to `true`
- `transformRightExpr` - defaults to `true`

Thanks to that, the user can specify which side of the expression should be transformed.
The `transformLeftExpr()` and `transformRightExpr()` methods are called only when necessary. For example:

```php
$expression = new Expr\Comparison('foo', '=', 'bar');

// LOWER(foo) = LOWER(bar)
(new LowerExpressionTransformer())
    ->transform($expression);

// foo = LOWER(bar)
(new LowerExpressionTransformer(transformLeftExpr: false, transformRightExpr: true))
    ->transform($expression);

// LOWER(foo) = bar
(new LowerExpressionTransformer(transformLeftExpr: true, transformRightExpr: false))
    ->transform($expression);
```

To use the created expression transformer, pass it as the `expression_transformers` filter type option:

```php
use App\DataTable\Filter\ExpressionTransformer\UnaccentExpressionTransformer;
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type\TextFilterType;

class ProductDataTableType extends AbstractDataTableType
{
    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        $builder
            ->addFilter('name', TextFilterType::class, [
                'expression_transformers' => [
                    new UnaccentExpressionTransformer(),
                ],
            ])
        ;
    }
}
```

## Adding an option to automatically apply transformer

Following the above example of `UnaccentExpressionTransformer`, let's assume, that we want to create such definition:

```php
use App\DataTable\Filter\ExpressionTransformer\UnaccentExpressionTransformer;
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type\TextFilterType;

class ProductDataTableType extends AbstractDataTableType
{
    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        $builder
            ->addFilter('name', TextFilterType::class, [
                'unaccent' => true,
            ])
        ;
    }
}
```

To achieve that, create a custom filter type extension:

```php
use App\DataTable\Filter\ExpressionTransformer\UnaccentExpressionTransformer;
use Kreyu\Bundle\DataTableBundle\Filter\Extension\AbstractFilterTypeExtension;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type\DoctrineOrmFilterType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\Options;

class UnaccentFilterTypeExtension extends AbstractFilterTypeExtension
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('unaccent', false)
            ->setAllowedTypes('unaccent', 'bool')
            ->addNormalizer('expression_transformers', function (Options $options, array $expressionTransformers) {
                if ($options['unaccent']) {
                    $expressionTransformers[] = new UnaccentExpressionTransformer();
                }
                
                return $expressionTransformers;
            })
        ;
    }
    
    public static function getExtendedTypes(): iterable
    {
        return [DoctrineOrmFilterType::class];
    }
}
```

The `unaccent` option is now defined, and defaults to `false`. In addition, the options resolver normalizer will automatically push an instance
of the custom expression transformer to the `expression_transformers` option, but only if the `unaccent` option equals `true`.
