<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Transform\Rector\Class_\ParentClassToTraitsRector;
use Rector\Transform\ValueObject\ParentClassToTraits;

// @see https://doc.nette.org/en/2.4/migration-2-4#toc-nette-smartobject
return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->ruleWithConfiguration(ParentClassToTraitsRector::class, [
        new ParentClassToTraits('Nette\Object', ['Nette\SmartObject']),
    ]);
};
