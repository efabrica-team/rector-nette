<?php

declare(strict_types=1);

use Rector\Renaming\Rector\MethodCall\RenameMethodRector;
use Rector\Renaming\Rector\Name\RenameClassRector;
use Rector\Renaming\ValueObject\MethodCallRename;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(RenameMethodRector::class)
        ->configure([new MethodCallRename('Kdyby\RabbitMq\IConsumer', 'process', 'execute')]);

    $services->set(RenameClassRector::class)
        ->configure([
            'Kdyby\RabbitMq\IConsumer' => 'OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface',
            'Kdyby\RabbitMq\IProducer' => 'OldSound\RabbitMqBundle\RabbitMq\ProducerInterface',
        ]);
};
