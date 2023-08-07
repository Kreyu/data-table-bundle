<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\DataTransformer;

use Kreyu\Bundle\DataTableBundle\Exception\TransformationFailedException;
use Symfony\Component\Form\Extension\Core\DataTransformer\MoneyToLocalizedStringTransformer as FormMoneyToLocalizedStringTransformer;

/**
 * Implementation copied from {@see FormMoneyToLocalizedStringTransformer}.
 */
class MoneyToLocalizedStringTransformer extends NumberToLocalizedStringTransformer
{
    public function __construct(
        private readonly ?int    $scale = null,
        private readonly ?int    $roundingMode = \NumberFormatter::ROUND_HALFUP,
        private readonly ?string $decimalSeparatorSymbol = null,
        private readonly ?string $thousandsSeparatorSymbol = null,
        private readonly ?string $locale = null,
        private readonly int $divisor = 1,
    ) {
        parent::__construct(
            scale: $this->scale ?? 2,
            roundingMode: $this->roundingMode,
            decimalSeparatorSymbol: $this->decimalSeparatorSymbol,
            thousandsSeparatorSymbol: $this->thousandsSeparatorSymbol,
            locale: $this->locale,
        );
    }

    public function transform(mixed $value): string
    {
        if (null !== $value && 1 !== $this->divisor) {
            if (!is_numeric($value)) {
                throw new TransformationFailedException('Expected a numeric.');
            }

            $value /= $this->divisor;
        }

        return parent::transform($value);
    }
}