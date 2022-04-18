<?php

declare(strict_types=1);

use Rector\Nette\Kdyby\Rector\ClassMethod\ChangeNetteEventNamesInGetSubscribedEventsRector;

return static function (\Rector\Config\RectorConfig $rectorConfig): void {
    $rectorConfig->import(__DIR__ . '/../../../../../../config/config.php');
    $services = $rectorConfig->services();

    $services->set(ChangeNetteEventNamesInGetSubscribedEventsRector::class);
};
