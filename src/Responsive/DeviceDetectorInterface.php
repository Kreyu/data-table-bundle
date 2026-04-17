<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Responsive;

use Symfony\Component\HttpFoundation\Request;

interface DeviceDetectorInterface
{
    public function detect(Request $request): Device;
}
