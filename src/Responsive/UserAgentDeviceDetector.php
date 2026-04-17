<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Responsive;

use Symfony\Component\HttpFoundation\Request;

class UserAgentDeviceDetector implements DeviceDetectorInterface
{
    private const TABLET_PATTERN = '/iPad|Android(?!.*Mobile)|Tablet|PlayBook|Silk/i';
    private const PHONE_PATTERN = '/Mobile|iPhone|iPod|Android.*Mobile|webOS|BlackBerry|Opera Mini|IEMobile|Windows Phone/i';

    public function detect(Request $request): Device
    {
        $userAgent = $request->headers->get('User-Agent', '');

        if (preg_match(self::TABLET_PATTERN, $userAgent)) {
            return Device::Tablet;
        }

        if (preg_match(self::PHONE_PATTERN, $userAgent)) {
            return Device::Phone;
        }

        return Device::Desktop;
    }
}
