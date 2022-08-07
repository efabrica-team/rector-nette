<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Nette\Rector\ClassMethod\TemplateMagicAssignToExplicitVariableArrayRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->rule(TemplateMagicAssignToExplicitVariableArrayRector::class);
};
