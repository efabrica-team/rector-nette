<?php

declare(strict_types=1);

use Rector\Core\Configuration\Option;
use Rector\Nette\Rector\Assign\MakeGetComponentAssignAnnotatedRector;

return static function (\Rector\Config\RectorConfig $rectorConfig): void {
    $rectorConfig->import(__DIR__ . '/../../../../../config/config.php');

    $parameters = $rectorConfig->parameters();
    $parameters->set(Option::AUTO_IMPORT_NAMES, true);

    $rectorConfig->rule(MakeGetComponentAssignAnnotatedRector::class);
};
