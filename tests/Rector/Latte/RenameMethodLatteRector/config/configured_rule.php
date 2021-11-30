<?php

declare(strict_types=1);

use Rector\Nette\Tests\Rector\Latte\RenameMethodLatteRector\Source\SomeClass;
use Rector\Renaming\Rector\MethodCall\RenameMethodRector;
use Rector\Renaming\ValueObject\MethodCallRename;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->import(__DIR__ . '/../../../../../config/config.php');

    $services = $containerConfigurator->services();

    $services->set(RenameMethodRector::class)
        ->configure([new MethodCallRename(SomeClass::class, 'get', 'getAll')]);
};
