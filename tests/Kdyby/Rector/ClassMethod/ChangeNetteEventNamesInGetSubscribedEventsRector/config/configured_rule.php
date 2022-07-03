<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Nette\Kdyby\Rector\ClassMethod\ChangeNetteEventNamesInGetSubscribedEventsRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->import(__DIR__ . '/../../../../../../config/config.php');

    $rectorConfig->rule(ChangeNetteEventNamesInGetSubscribedEventsRector::class);
};
