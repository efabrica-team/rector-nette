<?php

namespace Rector\Nette\Latte\Parser;

use Nette\Utils\Strings;
use PHPStan\BetterReflection\Reflection\ReflectionClass;
use PHPStan\BetterReflection\Reflection\ReflectionNamedType;
use Rector\Nette\ValueObject\LatteVariableType;

final class TemplateTypeParser
{
    /**
     * @return LatteVariableType[]
     */
    public function parse(string $content): array
    {
        $templateTypePattern = '#{templateType (?P<template>.*?)}#';
        $templateTypeMatch = Strings::match($content, $templateTypePattern);
        if (! isset($templateTypeMatch['template'])) {
            return [];
        }

        $variableTypes = [];
        $reflectionClass = ReflectionClass::createFromName($templateTypeMatch['template']);
        foreach ($reflectionClass->getProperties() as $property) {
            /** @var ReflectionNamedType $type */
            $type = $property->getType();
            $variableTypes[] = new LatteVariableType($property->getName(), $type);
        }
        return $variableTypes;
    }
}
