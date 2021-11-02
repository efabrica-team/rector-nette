<?php

declare(strict_types=1);

use Rector\Core\Configuration\Option;
use Rector\Php55\Rector\String_\StringClassNameToClassConstantRector;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    $parameters->set(Option::AUTO_IMPORT_NAMES, true);
    $parameters->set(Option::PATHS, [
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ]);
    $parameters->set(Option::SKIP, [
        // for tests
        '*/Source/*',
        '*/Fixture/*',
    ]);

    $services = $containerConfigurator->services();
    $services->set(StringClassNameToClassConstantRector::class)
        ->call('configure', [[
            StringClassNameToClassConstantRector::CLASSES_TO_SKIP => [
                'Nette\*',
                'Symfony\Component\Translation\TranslatorInterface',
                'Symfony\Contracts\EventDispatcher\Event',
                'Kdyby\Events\Subscriber',
            ]
        ]]);

    // needed for DEAD_CODE list, just in split package like this
    $containerConfigurator->import(__DIR__ . '/config/config.php');

    $containerConfigurator->import(LevelSetList::UP_TO_PHP_80);
    $containerConfigurator->import(SetList::DEAD_CODE);
};
