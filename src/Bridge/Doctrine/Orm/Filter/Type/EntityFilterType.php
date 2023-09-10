<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\Query\Expr;
use Kreyu\Bundle\DataTableBundle\Exception\InvalidArgumentException;
use Kreyu\Bundle\DataTableBundle\Filter\FilterData;
use Kreyu\Bundle\DataTableBundle\Filter\FilterInterface;
use Kreyu\Bundle\DataTableBundle\Filter\Operator;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyAccess;

class EntityFilterType extends AbstractDoctrineOrmFilterType
{
    public function __construct(
        private readonly Registry $doctrineRegistry,
    ) {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'form_type' => EntityType::class,
                'supported_operators' => [
                    Operator::Equals,
                    Operator::NotEquals,
                    Operator::Contains,
                    Operator::NotContains,
                ],
                'choice_label' => null,
                'active_filter_formatter' => $this->getFormattedActiveFilterString(...),
            ])
            ->setAllowedTypes('choice_label', ['null', 'string', 'callable'])
            ->addNormalizer('form_options', function (Options $options, array $value) {
                if (EntityType::class !== $options['form_type']) {
                    return $value;
                }

                // The identifier field name of the entity has to be provided in the 'choice_value' form option.
                //
                // This is required by the persistence system, because only the entity identifier will be persisted,
                // and the EntityType form type needs to know how to convert it back to the entity object.
                //
                // If it's not provided, try to retrieve it from the entity metadata.
                if (null === $value['choice_value'] ?? null) {
                    $identifiers = $this->doctrineRegistry
                        ->getManagerForClass($value['class'])
                        ?->getClassMetadata($value['class'])
                        ->getIdentifier() ?? [];

                    if (1 === count($identifiers)) {
                        $value += ['choice_value' => reset($identifiers)];
                    }
                }

                return $value;
            })
        ;
    }

    protected function getOperatorExpression(string $queryPath, string $parameterName, Operator $operator, Expr $expr): object
    {
        $expression = match ($operator) {
            Operator::Equals, Operator::Contains => $expr->in(...),
            Operator::NotEquals, Operator::NotContains => $expr->notIn(...),
            default => throw new InvalidArgumentException('Operator not supported'),
        };

        return $expression($queryPath, ":$parameterName");
    }

    private function getFormattedActiveFilterString(FilterData $data, FilterInterface $filter, array $options): string
    {
        $choiceLabel = $options['choice_label'];

        if (is_string($choiceLabel)) {
            return PropertyAccess::createPropertyAccessor()->getValue($data->getValue(), $choiceLabel);
        }

        if (is_callable($choiceLabel)) {
            return $choiceLabel($data->getValue());
        }

        return (string) $data->getValue();
    }
}
