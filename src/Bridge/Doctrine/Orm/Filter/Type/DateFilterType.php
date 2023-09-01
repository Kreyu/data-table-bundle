<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type;

use Doctrine\ORM\Query\Expr;
use Kreyu\Bundle\DataTableBundle\Exception\InvalidArgumentException;
use Kreyu\Bundle\DataTableBundle\Filter\FilterData;
use Kreyu\Bundle\DataTableBundle\Filter\FilterInterface;
use Kreyu\Bundle\DataTableBundle\Filter\Operator;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DateFilterType extends AbstractFilterType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'form_type' => DateType::class,
                'supported_operators' => [
                    Operator::Equals,
                    Operator::NotEquals,
                    Operator::GreaterThan,
                    Operator::GreaterThanEquals,
                    Operator::LessThan,
                    Operator::LessThanEquals,
                ],
                'active_filter_formatter' => $this->getFormattedActiveFilterString(...),
            ])
            ->addNormalizer('form_options', function (Options $options, array $value): array {
                return $value + ['widget' => 'single_text'];
            })
            ->addNormalizer('empty_data', function (Options $options, string|array $value): string|array {
                if (DateType::class !== $options['form_type']) {
                    return $value;
                }

                // Note: because choice and text widgets are split into three fields,
                //       we have to return an array with three empty values to properly set the empty data.
                return match ($options['form_options']['widget'] ?? null) {
                    'choice', 'text' => ['day' => '', 'month' => '', 'year' => ''],
                    default => '',
                };
            })
        ;
    }

    protected function getFilterValue(FilterData $data): \DateTimeInterface
    {
        $value = $data->getValue();

        if ($value instanceof \DateTimeInterface) {
            $dateTime = $value;
        } elseif (is_string($value)) {
            $dateTime = \DateTime::createFromFormat('Y-m-d', $value);
        } elseif (is_array($value)) {
            $dateTime = (new \DateTime())->setDate(
                year: (int) $value['date']['year'] ?: 0,
                month: (int) $value['date']['month'] ?: 0,
                day: (int) $value['date']['day'] ?: 0,
            );
        } else {
            throw new InvalidArgumentException(sprintf('Unable to convert data of type "%s" to DateTime object.', get_debug_type($value)));
        }

        $dateTime = \DateTime::createFromInterface($dateTime);
        $dateTime->setTime(0, 0);

        return $dateTime;
    }

    protected function getOperatorExpression(string $queryPath, string $parameterName, Operator $operator, Expr $expr): object
    {
        $expression = match ($operator) {
            Operator::Equals => $expr->eq(...),
            Operator::NotEquals => $expr->neq(...),
            Operator::GreaterThan => $expr->gt(...),
            Operator::GreaterThanEquals => $expr->gte(...),
            Operator::LessThan => $expr->lt(...),
            Operator::LessThanEquals => $expr->lte(...),
            default => throw new InvalidArgumentException('Operator not supported'),
        };

        return $expression($queryPath, ":$parameterName");
    }

    private function getFormattedActiveFilterString(FilterData $data, FilterInterface $filter, array $options): string
    {
        $value = $data->getValue();

        if ($value instanceof \DateTimeInterface) {
            return $value->format($options['field_options']['input_format'] ?? 'Y-m-d');
        }

        return (string) $value;
    }
}
