<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type;

use Doctrine\Persistence\ManagerRegistry;
use Kreyu\Bundle\DataTableBundle\Filter\FilterBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Filter\Operator;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Formatter\EntityActiveFilterFormatter;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EntityFilterType extends AbstractDoctrineOrmFilterType
{
    public function __construct(
        private readonly ?ManagerRegistry $managerRegistry = null,
    ) {
    }

    public function buildFilter(FilterBuilderInterface $builder, array $options): void
    {
        $builder->setSupportedOperators([
            Operator::Equals,
            Operator::NotEquals,
            Operator::In,
            Operator::NotIn,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'form_type' => EntityType::class,
                'choice_label' => null,
                'active_filter_formatter' => new EntityActiveFilterFormatter(),
            ])
            ->setAllowedTypes('choice_label', ['null', 'string', 'callable'])
        ;

        // The persistence feature is saving the identifier of the entity, not the entire selected entity.
        // Therefore, the EntityType requires "choice_value" option with a name of the entity identifier field.
        if (null !== $this->managerRegistry) {
            $resolver->addNormalizer('form_options', function (Options $options, array $formOptions) {
                if (EntityType::class !== $options['form_type'] || null === $class = $formOptions['class'] ?? null) {
                    return $formOptions;
                }

                $identifiers = $this->managerRegistry
                    ->getManagerForClass($class)
                    ?->getClassMetadata($class)
                    ->getIdentifier() ?? [];

                if (1 === count($identifiers)) {
                    $formOptions += ['choice_value' => reset($identifiers)];
                }

                return $formOptions;
            });
        }
    }
}
