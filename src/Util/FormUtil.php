<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Util;

use Symfony\Component\Form\FormView;

class FormUtil
{
    public static function getFormViewValueRecursive(FormView $view): mixed
    {
        $value = $view->vars['value'];

        if (!empty($view->children)) {
            $value = [];

            foreach ($view->children as $child) {
                if (isset($child->vars['checked'])) {
                    continue;
                }

                $value[$child->vars['name']] = static::getFormViewValueRecursive($child);
            }
        }

        return $value;
    }
}
