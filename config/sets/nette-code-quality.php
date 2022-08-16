<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Transform\Rector\FuncCall\FuncCallToStaticCallRector;
use Rector\Transform\ValueObject\FuncCallToStaticCall;
use RectorNette\Rector\ClassMethod\TemplateMagicAssignToExplicitVariableArrayRector;
use RectorNette\Rector\FuncCall\JsonDecodeEncodeToNetteUtilsJsonDecodeEncodeRector;
use RectorNette\Rector\FuncCall\PregFunctionToNetteUtilsStringsRector;
use RectorNette\Rector\FuncCall\PregMatchFunctionToNetteUtilsStringsRector;
use RectorNette\Rector\FuncCall\SubstrStrlenFunctionToNetteUtilsStringsRector;
use RectorNette\Rector\LNumber\ReplaceTimeNumberWithDateTimeConstantRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->rule(TemplateMagicAssignToExplicitVariableArrayRector::class);

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
