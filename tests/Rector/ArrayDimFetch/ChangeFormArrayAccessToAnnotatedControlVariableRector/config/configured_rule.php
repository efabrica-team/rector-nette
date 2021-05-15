<?php

declare(strict_types=1);

use Rector\Core\Configuration\Option;
use Rector\Nette\Rector\ArrayDimFetch\ChangeFormArrayAccessToAnnotatedControlVariableRector;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->import(__DIR__ . '/../../../../../config/config.php');

    $parameters = $containerConfigurator->parameters();
    $parameters->set(Option::PHPSTAN_FOR_RECTOR_PATH, __DIR__ . '/nette-phpstan-for-rector.neon');

    $services = $containerConfigurator->services();
    $services->set(ChangeFormArrayAccessToAnnotatedControlVariableRector::class);
};
