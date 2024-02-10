<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type;

use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\EventListener\ApplyExpressionTransformers;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\DoctrineOrmFilterHandler;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\ExpressionTransformer\ExpressionTransformerInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FilterBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Filter\Type\AbstractFilterType;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class DoctrineOrmFilterType extends AbstractFilterType
{
    public function buildFilter(FilterBuilderInterface $builder, array $options): void
    {
        $builder->setHandler(new DoctrineOrmFilterHandler());
        $builder->addEventSubscriber(new ApplyExpressionTransformers());
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'trim' => false,
                'lower' => false,
                'upper' => false,
                'expression_transformers' => [],
            ])
            ->setAllowedTypes('trim', 'bool')
            ->setAllowedTypes('lower', 'bool')
            ->setAllowedTypes('upper', 'bool')
            ->setAllowedTypes('expression_transformers', ExpressionTransformerInterface::class.'[]')
        ;
    }
}
