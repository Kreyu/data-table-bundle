<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column\Type;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Represents a column with PHP enumeration as value.
 *
 * If Symfony Translator component is installed and the enumeration
 * implements the {@see TranslatableInterface}, it will be translated.
 *
 * @see https://data-table-bundle.swroblewski.pl/reference/types/column/enum
 */
final class EnumColumnType extends AbstractColumnType
{
    public function __construct(
        private ?TranslatorInterface $translator = null,
    ) {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('formatter', $this->format(...));
    }

    protected function format(\UnitEnum $enum): string
    {
        if ($enum instanceof TranslatableInterface && null !== $this->translator) {
            return $enum->trans($this->translator);
        }

        return $enum->name;
    }
}
