<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use RectorComposer\Rector\ChangePackageVersionComposerRector;
use RectorComposer\Rector\RemovePackageComposerRector;
use RectorComposer\ValueObject\PackageAndVersion;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->ruleWithConfiguration(ChangePackageVersionComposerRector::class, [
        // meta package
        new PackageAndVersion('nette/nette', '~3.1.0'),
        // https://github.com/nette/nette/blob/v3.0.0/composer.json vs https://github.com/nette/nette/blob/v3.1.0/composer.json
        new PackageAndVersion('nette/application', '~3.1.0'),
        new PackageAndVersion('nette/bootstrap', '~3.1.0'),
        new PackageAndVersion('nette/caching', '~3.1.0'),
        new PackageAndVersion('nette/database', '~3.1.0'),
        new PackageAndVersion('nette/di', '^3.0'),
        new PackageAndVersion('nette/finder', '^2.5'),
        new PackageAndVersion('nette/forms', '~3.1.0'),
        new PackageAndVersion('nette/http', '~3.1.0'),
        new PackageAndVersion('nette/mail', '~3.1.0'),
        new PackageAndVersion('nette/php-generator', '^3.5'),
        new PackageAndVersion('nette/robot-loader', '^3.3'),
        new PackageAndVersion('nette/safe-stream', '^2.4'),
        new PackageAndVersion('nette/security', '~3.1.0'),
        new PackageAndVersion('nette/tokenizer', '^3.0'),
        new PackageAndVersion('nette/utils', '^3.2'),
        new PackageAndVersion('latte/latte', '^2.9'),
        new PackageAndVersion('tracy/tracy', '^2.8'),

        // contributte
        new PackageAndVersion('contributte/console', '^0.9'),
        new PackageAndVersion('contributte/event-dispatcher', '^0.8'),
        new PackageAndVersion('contributte/event-dispatcher-extra', '^0.8'),

        // nettrine
        new PackageAndVersion('nettrine/annotations', '^0.7'),
        new PackageAndVersion('nettrine/cache', '^0.3'),
    ]);

    $rectorConfig->ruleWithConfiguration(RemovePackageComposerRector::class, [
        'nette/component-model', 'nette/neon',
    ]);
};
