<?php

declare(strict_types=1);

use Rector\Nette\Tests\NonPhpFile\NetteDINeonMethodCallRenamer\Source\SecondService;
use Rector\Renaming\Collector\MethodCallRenameCollector;
use Rector\Renaming\ValueObject\MethodCallRename;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\SymfonyPhpConfig\ValueObjectInliner;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->import(__DIR__ . '/../../../../config/config.php');
    $services = $containerConfigurator->services();

    $services->set(MethodCallRenameCollector::class)
        ->call('addMethodCallRename', [
            ValueObjectInliner::inline(
                new MethodCallRename(SecondService::class, 'add', 'addConfig'),
            ),
        ]);;
};
