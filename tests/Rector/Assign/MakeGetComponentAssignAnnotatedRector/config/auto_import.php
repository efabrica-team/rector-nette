<?php

declare(strict_types=1);

use Rector\Core\Configuration\Option;
use Rector\Nette\Rector\Assign\MakeGetComponentAssignAnnotatedRector;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->import(__DIR__ . '/../../../../../config/config.php');

    $parameters = $containerConfigurator->parameters();
    $parameters->set(Option::AUTO_IMPORT_NAMES, true);

    $services = $containerConfigurator->services();
    $services->set(MakeGetComponentAssignAnnotatedRector::class);
};
