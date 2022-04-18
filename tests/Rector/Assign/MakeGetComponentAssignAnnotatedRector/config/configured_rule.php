<?php

declare(strict_types=1);

use Rector\Nette\Rector\Assign\MakeGetComponentAssignAnnotatedRector;

return static function (\Rector\Config\RectorConfig $rectorConfig): void {
    $rectorConfig->import(__DIR__ . '/../../../../../config/config.php');

    $rectorConfig->rule(MakeGetComponentAssignAnnotatedRector::class);
};
