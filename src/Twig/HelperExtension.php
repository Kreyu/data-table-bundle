<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Twig;

use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Twig\TwigTest;

class HelperExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('twig_function_exists', $this->isTwigFunctionDefined(...), [
                'needs_environment' => true,
            ]),
            new TwigFunction('twig_filter_exists', $this->isTwigFilterDefined(...), [
                'needs_environment' => true,
            ]),
        ];
    }

    public function isTwigFunctionDefined(Environment $environment, string $name): bool
    {
        return null !== $environment->getFunction($name);
    }

    public function isTwigFilterDefined(Environment $environment, string $name): bool
    {
        return null !== $environment->getFilter($name);
    }
}
