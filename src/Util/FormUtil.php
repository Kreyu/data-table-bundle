<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Util;

use Symfony\Component\Form\FormView;

class FormUtil
{
    public static function getFormName(string $name): string
    {
        return str_replace(search: '.', replace: ':', subject: $name);
    }

    public static function getFormViewValueRecursive(FormView $view): mixed
    {
        $value = [];

        foreach ($view->children as $child) {
            if (array_key_exists('checked', $child->vars)) {
                continue;
            }

            /** @var string $childName */
            $childName = $child->vars['name'];

            $value[$childName] = static::getFormViewValueRecursive($child);
        }

        if ($value === []) {
            return $view->vars['value'];
        }

        return $value;
    }
}
