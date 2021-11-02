<?php

declare(strict_types=1);

namespace Rector\Nette\NeonParser\Services;

use Nette\Neon\Node;
use Nette\Neon\Node\ArrayItemNode;
use Nette\Neon\Node\ArrayNode;
use Nette\Neon\Node\EntityNode;

final class ServiceTypeResolver
{
    /**
     * @var string
     */
    private const FACTORY_KEYWORD = 'factory';

    /**
     * @var string
     */
    private const CLASS_KEYWORD = 'class';

    public function resolve(Node $serviceNode): string|null
    {
        if (! $serviceNode instanceof ArrayItemNode) {
            return null;
        }

        if (! $serviceNode->value instanceof ArrayNode) {
            return null;
        }

        foreach ($serviceNode->value->items as $serviceConfigurationItem) {
            if ($serviceConfigurationItem->key === null) {
                continue;
            }

            if ($serviceConfigurationItem->key->toString() === self::FACTORY_KEYWORD) {
                if ($serviceConfigurationItem->value instanceof EntityNode) {
                    return $serviceConfigurationItem->value->value->toString();
                }

                return $serviceConfigurationItem->value->toString();
            }

            if ($serviceConfigurationItem->key->toString() === self::CLASS_KEYWORD) {
                if ($serviceConfigurationItem->value instanceof EntityNode) {
                    return $serviceConfigurationItem->value->value->toString();
                }

                return $serviceConfigurationItem->value->toString();
            }
        }

        return null;
    }
}
