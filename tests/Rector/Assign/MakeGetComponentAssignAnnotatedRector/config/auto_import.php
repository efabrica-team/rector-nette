<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Core\Configuration\Option;
use Rector\Nette\Rector\Assign\MakeGetComponentAssignAnnotatedRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->import(__DIR__ . '/../../../../../config/config.php');

    $rectorConfig->importNames();

    $rectorConfig->rule(MakeGetComponentAssignAnnotatedRector::class);
};
