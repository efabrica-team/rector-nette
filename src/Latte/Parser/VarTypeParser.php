<?php

namespace Rector\Nette\Latte\Parser;

use Nette\Utils\Strings;
use Rector\Nette\ValueObject\LatteVariableType;

final class VarTypeParser
{
    /**
     * @return LatteVariableType[]
     */
    public function parse(string $content): array
    {
        $pattern = '#{varType (?P<type>.*?) \$(?P<variable>.*?)}#';
        $matches = Strings::matchAll($content, $pattern);

        $variableTypes = [];
        foreach ($matches as $match) {
            $variableTypes[] = new LatteVariableType($match['variable'], $match['type']);
        }
        return $variableTypes;
    }
}
