<?php

require __DIR__ . '/../vendor/autoload.php';

// make dump() useful and not nest infinity spam
use Tracy\Debugger;

Debugger::$maxDepth = 2;
