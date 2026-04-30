<?php

declare(strict_types=1);

$header = <<<'EOF'
    This file is part of package ang3/php-etl-engine
    
    This source file is subject to the MIT license that is bundled
    with this source code in the file LICENSE.
    EOF;

$finder = PhpCsFixer\Finder::create()
    ->ignoreDotFiles(false)
    ->ignoreVCSIgnored(true)
    ->in(__DIR__)
    ->exclude('tests/Fixtures')
    ->append([
        __DIR__.'/dev-tools/doc.php',
        // __DIR__.'/php-cs-fixer', disabled, as we want to be able to run bootstrap file even on lower PHP version, to show nice message
    ])
;

$config = new PhpCsFixer\Config();
$config
    ->setRiskyAllowed(true)
    ->setRules([
        '@Symfony' => true,
        'header_comment' => ['header' => $header],
    ])
    ->setFinder($finder)
;

return $config;
