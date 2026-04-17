<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Unit\Responsive;

use Kreyu\Bundle\DataTableBundle\Responsive\Device;
use Kreyu\Bundle\DataTableBundle\Responsive\UserAgentDeviceDetector;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class UserAgentDeviceDetectorTest extends TestCase
{
    private UserAgentDeviceDetector $detector;

    protected function setUp(): void
    {
        $this->detector = new UserAgentDeviceDetector();
    }

    #[DataProvider('providePhoneUserAgents')]
    public function testDetectsPhone(string $userAgent): void
    {
        $request = Request::create('/', server: ['HTTP_USER_AGENT' => $userAgent]);

        $this->assertSame(Device::Phone, $this->detector->detect($request));
    }

    public static function providePhoneUserAgents(): iterable
    {
        yield 'iPhone Safari' => ['Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Mobile/15E148 Safari/604.1'];
        yield 'Android Chrome Mobile' => ['Mozilla/5.0 (Linux; Android 14; Pixel 8) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Mobile Safari/537.36'];
        yield 'iPod' => ['Mozilla/5.0 (iPod touch; CPU iPhone OS 15_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/15.0 Mobile/15E148 Safari/604.1'];
        yield 'Windows Phone' => ['Mozilla/5.0 (Windows Phone 10.0; Android 6.0.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.102 Mobile Safari/537.36'];
        yield 'Opera Mini' => ['Opera/9.80 (J2ME/MIDP; Opera Mini/9.80/191.308; U; en) Presto/2.5.25 Version/10.54'];
        yield 'BlackBerry' => ['Mozilla/5.0 (BlackBerry; U; BlackBerry 9900; en) AppleWebKit/534.11+ (KHTML, like Gecko) Version/7.1.0.346 Mobile Safari/534.11+'];
    }

    #[DataProvider('provideTabletUserAgents')]
    public function testDetectsTablet(string $userAgent): void
    {
        $request = Request::create('/', server: ['HTTP_USER_AGENT' => $userAgent]);

        $this->assertSame(Device::Tablet, $this->detector->detect($request));
    }

    public static function provideTabletUserAgents(): iterable
    {
        yield 'iPad Safari' => ['Mozilla/5.0 (iPad; CPU OS 17_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Mobile/15E148 Safari/604.1'];
        yield 'Android Tablet' => ['Mozilla/5.0 (Linux; Android 13; SM-X200) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36'];
        yield 'Kindle Fire' => ['Mozilla/5.0 (Linux; Android 11; Silk/95.3.7) AppleWebKit/537.36 (KHTML, like Gecko) Silk/95.3.7 like Chrome/95.0.4638.74 Safari/537.36'];
        yield 'PlayBook' => ['Mozilla/5.0 (PlayBook; U; RIM Tablet OS 2.1.0; en-US) AppleWebKit/536.2+ (KHTML like Gecko) Version/7.2.1.0 Safari/536.2+'];
    }

    #[DataProvider('provideDesktopUserAgents')]
    public function testDetectsDesktop(string $userAgent): void
    {
        $request = Request::create('/', server: ['HTTP_USER_AGENT' => $userAgent]);

        $this->assertSame(Device::Desktop, $this->detector->detect($request));
    }

    public static function provideDesktopUserAgents(): iterable
    {
        yield 'Chrome Windows' => ['Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36'];
        yield 'Firefox Linux' => ['Mozilla/5.0 (X11; Linux x86_64; rv:120.0) Gecko/20100101 Firefox/120.0'];
        yield 'Safari macOS' => ['Mozilla/5.0 (Macintosh; Intel Mac OS X 14_0) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Safari/605.1.15'];
        yield 'Edge Windows' => ['Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36 Edg/120.0.0.0'];
    }

    public function testEmptyUserAgentDefaultsToDesktop(): void
    {
        $request = Request::create('/');
        $request->headers->remove('User-Agent');

        $this->assertSame(Device::Desktop, $this->detector->detect($request));
    }
}
