<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Exporter;

use Symfony\Component\HttpFoundation\File\File;

class ExportFile extends File
{
    public function __construct(
        string $path,
        private string $filename,
    ) {
        parent::__construct($path);
    }

    public function getFilename(): string
    {
        return $this->filename ?? $this->getPathname();
    }
}
