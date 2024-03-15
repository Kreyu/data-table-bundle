<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Unit\Util;

use Kreyu\Bundle\DataTableBundle\Util\StringUtil;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class StringUtilTest extends TestCase
{
    #[DataProvider('provideFqcnToShortNameCases')]
    public function testFqcnToShortName(string $fqcn, array $suffixes, string $shortName)
    {
        $this->assertSame($shortName, StringUtil::fqcnToShortName($fqcn, $suffixes));
    }

    public static function provideFqcnToShortNameCases(): iterable
    {
        return [
            ['ProductDataTableType', ['DataTableType', 'Type'], 'product'],
            ['ProductType', ['DataTableType', 'Type'], 'product'],
            ['ProductCategoryDataTableType', ['DataTableType', 'Type'], 'product_category'],
            ['ProductCategoryType', ['DataTableType', 'Type'], 'product_category'],
        ];
    }

    #[DataProvider('provideCamelToSentenceCases')]
    public function testCamelToSentence(string $camel, string $sentence)
    {
        $this->assertSame($sentence, StringUtil::camelToSentence($camel));
    }

    public static function provideCamelToSentenceCases(): iterable
    {
        return [
            ['name', 'Name'],
            ['firstName', 'First name'],
            ['addressLine1', 'Address line 1'],
            ['addressLine2', 'Address line 2'],
            ['address1Line2', 'Address 1 line 2'],
            ['addressLine12', 'Address line 12'],
            ['address12Line34', 'Address 12 line 34'],
        ];
    }
}
