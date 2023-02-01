<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type;

use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Query\ProxyQueryInterface as DoctrineOrmProxyQueryInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FilterData;
use Kreyu\Bundle\DataTableBundle\Filter\FilterInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CallbackType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('operator_options', [
                'visible' => false,
                'choices' => [],
            ])
            ->setRequired('callback')
            ->setAllowedTypes('callback', ['callable'])
        ;
    }

    protected function filter(DoctrineOrmProxyQueryInterface $query, FilterData $data, FilterInterface $filter): void
    {
        $callback = $filter->getOption('callback');
        $callback($query, $data, $filter);
    }
}
