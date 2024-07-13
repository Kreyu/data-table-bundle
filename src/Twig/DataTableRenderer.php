<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Twig;

use Kreyu\Bundle\DataTableBundle\DataTableView;
use Twig\Environment;
use Twig\Error\RuntimeError;

class DataTableRenderer
{
    public function renderBlock(Environment $environment, DataTableView $view, string $blockName, array $context = []): string
    {
        foreach ($view->vars['themes'] as $theme) {
            $wrapper = $environment->load($theme);

            if ($wrapper->hasBlock($blockName, $context)) {
                return $wrapper->renderBlock($blockName, $context);
            }
        }

        throw new RuntimeError(sprintf('Block "%s" does not exist in any of the data table theme templates.', $blockName));
    }
}