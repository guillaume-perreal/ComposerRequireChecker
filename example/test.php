<?php

use ComposerRequireChecker\ASTLocator\LocateASTFromFiles;
use ComposerRequireChecker\DefinedSymbolsLocator\LocateDefinedSymbolsFromASTRoots;
use ComposerRequireChecker\FileLocator\LocateComposerPackageDirectDependenciesSourceFiles;
use ComposerRequireChecker\FileLocator\LocateComposerPackageSourceFiles;
use ComposerRequireChecker\UsedSymbolsLocator\LocateUsedSymbolsFromASTRoots;
use PhpParser\ParserFactory;

(function () {
    require_once  __DIR__ . '/../vendor/autoload.php';

    $getPackageSourceFiles = new LocateComposerPackageSourceFiles();

    $sourcesASTs  = new LocateASTFromFiles((new ParserFactory())->create(ParserFactory::PREFER_PHP7));
    $composerJson = __DIR__ . '/test-data/zend-feed/composer.json';

    $definedSymbols = (new LocateDefinedSymbolsFromASTRoots())->__invoke($sourcesASTs(
        (new \ComposerRequireChecker\GeneratorUtil\ComposeGenerators())->__invoke(
            $getPackageSourceFiles($composerJson),
            (new LocateComposerPackageDirectDependenciesSourceFiles())->__invoke($composerJson)
        )
    ));
    $usedSymbols = (new LocateUsedSymbolsFromASTRoots())->__invoke($sourcesASTs($getPackageSourceFiles($composerJson)));

    var_dump([
        'defined'         => $definedSymbols,
        'used'            => $usedSymbols,
        'unknown_symbols' => array_diff($usedSymbols, $definedSymbols),
    ]);
})();