<?php

declare(strict_types=1);

use Rector\Nette\Set\NetteSetList;

return static function (\Rector\Config\RectorConfig $rectorConfig): void {
    $rectorConfig->import(__DIR__ . '/../../../../config/config.php');
    $rectorConfig->import(NetteSetList::NETTE_31);
};
