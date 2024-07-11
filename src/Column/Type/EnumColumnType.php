<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column\Type;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class EnumColumnType extends AbstractColumnType
{
    public function __construct(
        private ?TranslatorInterface $translator,
    ) {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('formatter', $this->format(...));
    }

    protected function format(\UnitEnum $enum): string
    {
        return $enum instanceof TranslatableInterface ? $enum->trans($this->translator) : $enum->name;
    }

    public function getParent(): ?string
    {
        return TextColumnType::class;
    }
}
