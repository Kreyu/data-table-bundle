<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Formatter;

use Kreyu\Bundle\DataTableBundle\Exception\InvalidArgumentException;
use Kreyu\Bundle\DataTableBundle\Exception\RuntimeException;
use Kreyu\Bundle\DataTableBundle\Filter\FilterData;
use Kreyu\Bundle\DataTableBundle\Filter\FilterInterface;

class DateActiveFilterFormatter
{
    public const DATE_SKELETON = 'yMMMd';

    private const FALLBACK_FORMATS = [
        'yMMMd' => 'Y-m-d',
        'yMMMdj' => 'Y-m-d H',
        'yMMMdjm' => 'Y-m-d H:i',
        'yMMMdjms' => 'Y-m-d H:i:s',
    ];

    public function __invoke(FilterData $data, FilterInterface $filter): string
    {
        $value = $data->getValue();

        if ($value instanceof \DateTimeInterface) {
            /** @var string|null $inputFormat */
            $inputFormat = $filter->getConfig()->getOption('form_options')['input_format'] ?? null;

            if ($inputFormat !== null) {
                return $value->format($inputFormat);
            }

            return $this->format($value, self::DATE_SKELETON);
        }

        return (string) $value;
    }

    public function format(\DateTimeInterface $date, string $skeleton): string
    {
        $isIntlLoaded = extension_loaded('intl');

        if (!$isIntlLoaded) {
            return $date->format($this->getFallbackFormat($skeleton));
        }

        $pattern = (new \IntlDatePatternGenerator())->getBestPattern($skeleton);

        if ($pattern === false) {
            return $date->format($this->getFallbackFormat($skeleton));
        }

        $formatter = new \IntlDateFormatter(
            null,
            \IntlDateFormatter::NONE,
            \IntlDateFormatter::NONE,
            $date->getTimezone(),
            null,
            $pattern,
        );

        $formattedDate = $formatter->format($date);

        if ($formattedDate === false) {
            throw new RuntimeException(sprintf(
                'Formatting the date with pattern "%s" failed: %s.',
                $pattern,
                $formatter->getErrorMessage(),
            ));
        }

        return $formattedDate;
    }

    private function getFallbackFormat(string $skeleton): string
    {
        $isSupportedSkeleton = array_key_exists($skeleton, self::FALLBACK_FORMATS);

        if (!$isSupportedSkeleton) {
            throw new InvalidArgumentException(sprintf(
                'Skeleton "%s" has no fallback format without the "intl" extension. Supported skeletons: "%s".',
                $skeleton,
                implode('", "', array_keys(self::FALLBACK_FORMATS)),
            ));
        }

        return self::FALLBACK_FORMATS[$skeleton];
    }
}
