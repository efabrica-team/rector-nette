<?php

declare(strict_types=1);

use Rector\Nette\Rector\Neon\RenameMethodNeonRector;
use Rector\Nette\Tests\Rector\Neon\RenameMethodNeonRector\Source\SecondService;
use Rector\Renaming\Rector\MethodCall\RenameMethodRector;
use Rector\Renaming\ValueObject\MethodCallRename;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\SymfonyPhpConfig\ValueObjectInliner;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->import(__DIR__ . '/../../../../../config/config.php');

    $services = $containerConfigurator->services();

    $services->set(RenameMethodRector::class)
        ->call('configure', [[
            RenameMethodRector::METHOD_CALL_RENAMES => ValueObjectInliner::inline([
                new MethodCallRename(SecondService::class, 'add', 'addConfig'),
            ]),
        ]]);

    $services->set(RenameMethodNeonRector::class);
};