<?php

declare(strict_types=1);

use Rector\Nette\Rector\Latte\RenameMethodLatteRector;
use Rector\Nette\Tests\Rector\Latte\RenameMethodLatteRector\Source\SomeClass;
use Rector\Renaming\ValueObject\MethodCallRename;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\SymfonyPhpConfig\ValueObjectInliner;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->import(__DIR__ . '/../../../../../config/config.php');

    $services = $containerConfigurator->services();

    $services->set(RenameMethodLatteRector::class)
        ->call('configure', [[
            RenameMethodLatteRector::RENAME_METHODS => ValueObjectInliner::inline([
                new MethodCallRename(SomeClass::class, 'get', 'getAll'),
            ]),
        ]]);
};
