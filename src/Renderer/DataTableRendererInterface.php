<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Renderer;

interface DataTableRendererInterface
{
    public function renderHeaders(): string;

    public function renderRow(): string;
}