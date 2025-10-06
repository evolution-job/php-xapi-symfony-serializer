<?php

use Rector\Config\RectorConfig;
use Rector\PHPUnit\Set\PHPUnitSetList;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;
use Rector\Symfony\Symfony61\Rector\Class_\CommandConfigureToAttributeRector;

return RectorConfig::configure()
    ->withParallel(360, 8)
    ->withPaths([
        __DIR__ . '/spec',
        __DIR__ . '/src',
        __DIR__ . '/tests'
    ])
    ->withImportNames()
    ->withComposerBased(symfony: true)
    ->withRules([
        CommandConfigureToAttributeRector::class
    ])
    ->withSets([
        LevelSetList::UP_TO_PHP_84,
        SetList::CODE_QUALITY,
        SetList::CODING_STYLE,
        SetList::DEAD_CODE,
        SetList::EARLY_RETURN,
        SetList::INSTANCEOF,
        SetList::STRICT_BOOLEANS,
        SetList::TYPE_DECLARATION,
        PHPUnitSetList::PHPUNIT_110,
        PHPUnitSetList::PHPUNIT_CODE_QUALITY,
    ]);

// CLI cmd: php vendor/bin/rector process --memory-limit 3G --no-diffs
