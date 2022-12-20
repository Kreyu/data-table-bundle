<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Bridge\Twig;

use Kreyu\Bundle\DataTableBundle\View\DataTableViewInterface;
use Twig\Environment;
use Twig\Error\Error as TwigException;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class DataTableRendererExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'render_data_table',
                [$this, 'renderDataTable'],
                ['needs_environment' => true, 'is_safe' => ['html']],
            ),
        ];
    }

    /**
     * @throws TwigException
     */
    public function renderDataTable(Environment $environment, DataTableViewInterface $dataTable): string
    {
        return $environment->render('@KreyuDataTable/data_table.html.twig', [
            'data_table' => $dataTable,
        ]);
    }
}
