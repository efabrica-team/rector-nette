<?php

declare(strict_types=1);

use Rector\Nette\Rector\Class_\TemplateTypeBasedOnPresenterTemplateParametersRector;

return static function (\Rector\Config\RectorConfig $rectorConfig): void {
    $rectorConfig->import(__DIR__ . '/../../../../../config/config.php');

    $rectorConfig->rule(TemplateTypeBasedOnPresenterTemplateParametersRector::class);
};
