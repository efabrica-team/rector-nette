<?php

declare(strict_types=1);

use Rector\Php80\Rector\Class_\AnnotationToAttributeRector;
use Rector\Php80\ValueObject\AnnotationToAttribute;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\SymfonyPhpConfig\ValueObjectInliner;

// @see https://github.com/apitte/core/pull/161
return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(AnnotationToAttributeRector::class)
        ->call('configure', [[
            AnnotationToAttributeRector::ANNOTATION_TO_ATTRIBUTE => ValueObjectInliner::inline([
                new AnnotationToAttribute('Apitte\Core\Annotation\Controller\Id'),
                new AnnotationToAttribute('Apitte\Core\Annotation\Controller\Method'),
                new AnnotationToAttribute('Apitte\Core\Annotation\Controller\Negotiation'),
                new AnnotationToAttribute('Apitte\Core\Annotation\Controller\OpenApi'),
                new AnnotationToAttribute('Apitte\Core\Annotation\Controller\Path'),
                new AnnotationToAttribute('Apitte\Core\Annotation\Controller\RequestBody'),
                new AnnotationToAttribute('Apitte\Core\Annotation\Controller\RequestParameter'),
                new AnnotationToAttribute('Apitte\Core\Annotation\Controller\Response'),
                new AnnotationToAttribute('Apitte\Core\Annotation\Controller\Tag'),
            ]),
        ]]);
};
