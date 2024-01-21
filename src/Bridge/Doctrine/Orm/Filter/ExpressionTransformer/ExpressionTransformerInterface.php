<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\ExpressionTransformer;

interface ExpressionTransformerInterface
{
    public function transform(mixed $expression): mixed;
}
