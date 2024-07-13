<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Twig;

use Symfony\Component\OptionsResolver\OptionsResolver;

class Attributes implements \Stringable
{
    private array $defaults = [];
    private array $children = [];

    public function __toString(): string
    {
        $attr = $this->defaults;

        return implode(' ', array_map(function ($key) use ($attr) {
            $value = is_object($attr[$key]) ? (string) $attr[$key] : $attr[$key];
            $value = is_array($value) ? implode(' ', array_map(function ($item) {
                return (string) $item; // cast all second level array value to string.
            }, $value)) : (string) $value;
            return sprintf('%s="%s"', htmlspecialchars($key), htmlspecialchars($value));
        }, array_keys($attr)));
    }

    public function __invoke(array $attr): string
    {
        return implode(' ', array_map(function ($key) use ($attr) {
            $value = is_object($attr[$key]) ? (string) $attr[$key] : $attr[$key];
            $value = is_array($value) ? implode(' ', array_map(function ($item) {
                return (string) $item; // cast all second level array value to string.
            }, $value)) : (string) $value;
            return sprintf('%s="%s"', htmlspecialchars($key), htmlspecialchars($value));
        }, array_keys($attr)));
    }

    public function defaults(array $defaults)
    {
        $this->defaults = $defaults;
    }
}