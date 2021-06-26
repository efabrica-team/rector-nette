<?php

declare(strict_types=1);

use Rector\Nette\Kdyby\Rector\MethodCall\WrapTransParameterNameRector;
use Rector\Renaming\Rector\MethodCall\RenameMethodRector;
use Rector\Renaming\Rector\Name\RenameClassRector;
use Rector\Renaming\ValueObject\MethodCallRename;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\SymfonyPhpConfig\ValueObjectInliner;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(RenameMethodRector::class)
        ->call('configure', [[
            RenameMethodRector::METHOD_CALL_RENAMES => ValueObjectInliner::inline([
                new MethodCallRename('Kdyby\Translation\Translator', 'translate', 'trans'),
            ]),
        ]]);

    $services->set(RenameClassRector::class)
        ->call('configure', [[
            RenameClassRector::OLD_TO_NEW_CLASSES => [
                'Kdyby\Translation\Translator' => 'Nette\Localization\ITranslator',
                'Kdyby\Translation\DI\ITranslationProvider' => 'Contributte\Translation\DI\TranslationProviderInterface',
                'Kdyby\Translation\Phrase' => 'Contributte\Translation\Wrappers\Message',
            ],
        ]]);

    $services->set(WrapTransParameterNameRector::class);
};
