<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Service\Attribute\Required;
use Twig\Environment;

trait DataTableTurboResponseTrait
{
    protected ?Environment $twig = null;

    #[Required]
    public function setTwig(?Environment $twig): void
    {
        $this->twig = $twig;
    }

    public function createDataTableTurboResponse(
        DataTableInterface $dataTable,
    ): Response {
        return new Response(
            $this->twig->createTemplate('{{ data_table(dataTableContent) }}')->render([
                'dataTableContent' => $dataTable->createView(),
            ]),
        );
    }
}
