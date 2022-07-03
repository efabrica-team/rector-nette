<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Nette\Rector\Assign\ArrayAccessGetControlToGetComponentMethodCallRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->import(__DIR__ . '/../../../../../config/config.php');

    $rectorConfig->rule(ArrayAccessGetControlToGetComponentMethodCallRector::class);
};
