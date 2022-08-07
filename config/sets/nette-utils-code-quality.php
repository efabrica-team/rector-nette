<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Nette\Rector\FuncCall\JsonDecodeEncodeToNetteUtilsJsonDecodeEncodeRector;
use Rector\Nette\Rector\FuncCall\PregFunctionToNetteUtilsStringsRector;
use Rector\Nette\Rector\FuncCall\PregMatchFunctionToNetteUtilsStringsRector;
use Rector\Nette\Rector\FuncCall\SubstrStrlenFunctionToNetteUtilsStringsRector;
use Rector\Nette\Rector\LNumber\ReplaceTimeNumberWithDateTimeConstantRector;
use Rector\Transform\Rector\FuncCall\FuncCallToStaticCallRector;
use Rector\Transform\ValueObject\FuncCallToStaticCall;

# @see https://tomasvotruba.com/blog/2018/07/30/hidden-gems-of-php-packages-nette-utils
return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->ruleWithConfiguration(FuncCallToStaticCallRector::class, [
        new FuncCallToStaticCall('file_get_contents', 'Nette\Utils\FileSystem', 'read'),
        new FuncCallToStaticCall('unlink', 'Nette\Utils\FileSystem', 'delete'),
        new FuncCallToStaticCall('rmdir', 'Nette\Utils\FileSystem', 'delete'),
    ]);

    $rectorConfig->rule(SubstrStrlenFunctionToNetteUtilsStringsRector::class);
    $rectorConfig->rule(PregMatchFunctionToNetteUtilsStringsRector::class);
    $rectorConfig->rule(PregFunctionToNetteUtilsStringsRector::class);
    $rectorConfig->rule(JsonDecodeEncodeToNetteUtilsJsonDecodeEncodeRector::class);
    $rectorConfig->rule(ReplaceTimeNumberWithDateTimeConstantRector::class);
};
