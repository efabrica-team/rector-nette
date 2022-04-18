<?php

declare(strict_types=1);

use Rector\Nette\Kdyby\Rector\MethodCall\WrapTransParameterNameRector;

return static function (\Rector\Config\RectorConfig $rectorConfig): void {
    $rectorConfig->import(__DIR__ . '/../../../../../../config/config.php');

    $rectorConfig->rule(WrapTransParameterNameRector::class);
};
