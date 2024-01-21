<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Unit\Bridge\Doctrine\Orm\Fixtures\Filter\ExpressionTransformer;

use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\ExpressionTransformer\AbstractComparisonExpressionTransformer;

class CustomExpressionTransformer extends AbstractComparisonExpressionTransformer
{
    protected function transformLeftExpr(mixed $leftExpr): string
    {
        return sprintf('CUSTOM(%s)', $leftExpr);
    }

    protected function transformRightExpr(mixed $rightExpr): string
    {
        return sprintf('CUSTOM(%s)', $rightExpr);
    }
}
