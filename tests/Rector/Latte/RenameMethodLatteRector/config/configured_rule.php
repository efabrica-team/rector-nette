<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;

use Rector\Nette\Tests\Rector\Latte\RenameMethodLatteRector\Source\SomeClass;
use Rector\Renaming\Rector\MethodCall\RenameMethodRector;
use Rector\Renaming\ValueObject\MethodCallRename;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->import(__DIR__ . '/../../../../../config/config.php');

    $rectorConfig->ruleWithConfiguration(RenameMethodRector::class, [
        new MethodCallRename(SomeClass::class, 'get', 'getAll'),
    ]);
};
