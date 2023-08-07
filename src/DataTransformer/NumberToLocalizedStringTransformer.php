<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\DataTransformer;

use Kreyu\Bundle\DataTableBundle\DataTransformerInterface;
use Kreyu\Bundle\DataTableBundle\Exception\TransformationFailedException;
use Symfony\Component\Form\Extension\Core\DataTransformer\NumberToLocalizedStringTransformer as FormNumberToLocalizedStringTransformer;

/**
 * Implementation copied from {@see FormNumberToLocalizedStringTransformer}.
 *
 * @implements DataTransformerInterface<int|float, string>
 */
class NumberToLocalizedStringTransformer implements DataTransformerInterface
{
    public function __construct(
        private readonly ?int    $scale = null,
        private readonly ?int    $roundingMode = \NumberFormatter::ROUND_HALFUP,
        private readonly ?string $decimalSeparatorSymbol = null,
        private readonly ?string $thousandsSeparatorSymbol = null,
        private readonly ?string $locale = null,
    ) {
    }

    public function transform(mixed $value): string
    {
        if (null === $value) {
            return '';
        }

        if (!is_numeric($value)) {
            throw new TransformationFailedException('Expected a numeric.');
        }

        $formatter = $this->getNumberFormatter();
        $value = $formatter->format((float) $value);

        if (intl_is_failure($formatter->getErrorCode())) {
            throw new TransformationFailedException($formatter->getErrorMessage());
        }

        // Convert non-breaking and narrow non-breaking spaces to normal ones
        $value = str_replace(["\xc2\xa0", "\xe2\x80\xaf"], ' ', $value);

        return $value;
    }

    protected function getNumberFormatter(): \NumberFormatter
    {
        $formatter = new \NumberFormatter($this->locale ?? \Locale::getDefault(), \NumberFormatter::DECIMAL);

        if (null !== $this->scale) {
            $formatter->setAttribute(\NumberFormatter::FRACTION_DIGITS, $this->scale);
            $formatter->setAttribute(\NumberFormatter::ROUNDING_MODE, $this->roundingMode);
        }

        if (null !== $this->decimalSeparatorSymbol) {
            $formatter->setSymbol(\NumberFormatter::DECIMAL_SEPARATOR_SYMBOL, $this->decimalSeparatorSymbol);
        }

        if (null !== $this->thousandsSeparatorSymbol) {
            $formatter->setSymbol(\NumberFormatter::GROUPING_SEPARATOR_SYMBOL, $this->thousandsSeparatorSymbol);
        }

        return $formatter;
    }
}
