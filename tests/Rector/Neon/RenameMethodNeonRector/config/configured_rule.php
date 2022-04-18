<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;

use Rector\Nette\Tests\Rector\Neon\RenameMethodNeonRector\Source\SecondService;
use Rector\Renaming\Rector\MethodCall\RenameMethodRector;
use Rector\Renaming\ValueObject\MethodCallRename;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->import(__DIR__ . '/../../../../../config/config.php');

    $services = $rectorConfig->services();

    $services->set(RenameMethodRector::class)
        ->configure([new MethodCallRename(SecondService::class, 'add', 'addConfig')]);
};
