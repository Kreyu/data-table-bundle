<?php
declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Action;

interface ActionRefreshUrlGeneratorInterface
{
    public function generate(): string;
}