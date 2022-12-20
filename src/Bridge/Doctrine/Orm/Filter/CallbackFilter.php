<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter;

use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Query\ProxyQueryInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FilterData;
use Kreyu\Bundle\DataTableBundle\Filter\Operator;
use Kreyu\Bundle\DataTableBundle\Form\Type\OperatorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CallbackFilter extends AbstractFilter
{
    public function getFormOptions(): array
    {
        return array_merge(parent::getFormOptions(), [
            'operator_options' => [
                'visible' => false,
                'choices' => [],
            ],
        ]);
    }

    protected function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver
            ->setRequired('callback')
            ->setAllowedTypes('callback', ['callable', \Closure::class])
        ;
    }

    protected function filter(ProxyQueryInterface $query, FilterData $data): void
    {
        $callback = $this->getOption('callback');
        $callback($query, $data);
    }

    protected function getSupportedOperators(): array
    {
        // Allow all operators, since we are handling the query manually.
        return Operator::cases();
    }
}
