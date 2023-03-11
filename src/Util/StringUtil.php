<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Util;

class StringUtil
{
    /**
     * Converts a fully-qualified class name to a short name, removing the given suffix.
     *
     * For example, if :
     *  - $fqcn equals "App\\DataTable\\Type\\ProductDataTableType" or "App\\DataTable\\Type\\ProductType"
     *  - $suffixes equals ['DataTableType', 'Type']
     *
     * then the returned string will equal "product".
     *
     * @param string        $fqcn     The fully-qualified class name
     * @param array<string> $suffixes The suffixes removed from the fully-qualified class name
     */
    public static function fqcnToShortName(string $fqcn, array $suffixes): ?string
    {
        $suffixesExpression = implode('|', $suffixes);

        if (preg_match("~([^\\\\]+?)($suffixesExpression)?$~i", $fqcn, $matches)) {
            return strtolower(preg_replace(['/([A-Z]+)([A-Z][a-z])/', '/([a-z\d])([A-Z])/'], ['\\1_\\2', '\\1_\\2'], $matches[1]));
        }

        return null;
    }
}
