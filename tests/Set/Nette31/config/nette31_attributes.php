<?php

declare(strict_types=1);

use Rector\Nette\Set\NetteSetList;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->import(__DIR__ . '/../../../../config/config.php');
    $containerConfigurator->import(NetteSetList::NETTE_31);
};
