<?php

declare(strict_types=1);

namespace Rector\Nette\Set;

use Rector\Set\Contract\SetListInterface;

final class KdybySetList implements SetListInterface
{
    /**
     * @var string
     */
    final public const KDYBY_EVENTS_TO_CONTRIBUTTE_EVENT_DISPATCHER = __DIR__ . '/../../config/sets/kdyby/kdyby-events-to-contributte-event-dispatcher.php';
}
