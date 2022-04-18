<?php

declare(strict_types=1);

use Rector\Nette\Rector\ArrayDimFetch\AnnotateMagicalControlArrayAccessRector;

return static function (\Rector\Config\RectorConfig $rectorConfig): void {
    $rectorConfig->import(__DIR__ . '/../../../../../config/config.php');

    $rectorConfig->rule(AnnotateMagicalControlArrayAccessRector::class);
};
