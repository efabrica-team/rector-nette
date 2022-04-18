<?php

declare(strict_types=1);

use Rector\Nette\Tests\Rector\Latte\RenameMethodLatteRector\Source\SomeClass;
use Rector\Renaming\Rector\MethodCall\RenameMethodRector;
use Rector\Renaming\ValueObject\MethodCallRename;

return static function (\Rector\Config\RectorConfig $rectorConfig): void {
    $rectorConfig->import(__DIR__ . '/../../../../../config/config.php');

    $services = $rectorConfig->services();

    $services->set(RenameMethodRector::class)
        ->configure([new MethodCallRename(SomeClass::class, 'get', 'getAll')]);
};
