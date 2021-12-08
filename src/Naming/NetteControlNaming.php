<?php

declare(strict_types=1);

namespace Rector\Nette\Naming;

use Symfony\Component\String\UnicodeString;

final class NetteControlNaming
{
    public function createVariableName(string $shortName): string
    {
        $variableNameUnicodeString = new UnicodeString($shortName);
        $variableName = $variableNameUnicodeString->camel()
            ->toString();

        if (\str_ends_with($variableName, 'Form')) {
            return $variableName;
        }

        return $variableName . 'Control';
    }

    public function createCreateComponentClassMethodName(string $shortName): string
    {
        $shortNameUnicodeString = new UnicodeString($shortName);
        $componentName = $shortNameUnicodeString->upper()
            ->camel()
            ->toString();

        return 'createComponent' . $componentName;
    }
}
