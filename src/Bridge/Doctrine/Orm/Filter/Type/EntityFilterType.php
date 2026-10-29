<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type;

use Doctrine\Persistence\ManagerRegistry;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Formatter\EntityActiveFilterFormatter;
use Kreyu\Bundle\DataTableBundle\Filter\Operator;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class EntityFilterType extends AbstractDoctrineOrmFilterType
{
    public function __construct(
        private readonly ?ManagerRegistry $managerRegistry = null,
    ) {}

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'form_type' => EntityType::class,
                'supported_operators' => [
                    Operator::Equals,
                    Operator::NotEquals,
                    Operator::In,
                    Operator::NotIn,
                ],
                'choice_label' => null,
                'active_filter_formatter' => new EntityActiveFilterFormatter(),
            ])
            ->setAllowedTypes('choice_label', ['null', 'string', 'callable'])
        ;

        // The persistence feature is saving the identifier of the entity, not the entire selected entity.
        // Therefore, the EntityType requires "choice_value" option with a name of the entity identifier field.
        $managerRegistry = $this->managerRegistry;

        if ($managerRegistry !== null) {
            $resolver->addNormalizer('form_options', static function (Options $options, array $formOptions) use (
                $managerRegistry,
            ) {
                /** @var class-string|null $class */
                $class = $formOptions['class'] ?? null;

                /** @var string $formType */
                $formType = $options['form_type'];

                if ($formType !== EntityType::class) {
                    return $formOptions;
                }

                if ($class === null) {
                    return $formOptions;
                }

                $identifiers = $managerRegistry
                    ->getManagerForClass($class)
                    ?->getClassMetadata($class)
                    ->getIdentifier() ?? []
                ;

                $identifierCount = count($identifiers);

                if ($identifierCount === 1) {
                    $formOptions += ['choice_value' => reset($identifiers)];
                }

                return $formOptions;
            });
        }
    }
}
