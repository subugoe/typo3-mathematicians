<?php
$finder = PhpCsFixer\Finder::create()
    ->exclude('build')
    ->exclude('vendor')
    ->exclude('node_modules')
    ->in(__DIR__);
$config = PhpCsFixer\Config::create();
$config
    ->setRules([
        '@PSR2' => true,
        'array_syntax' => ['syntax' => 'short'],
    ])
    ->setFinder($finder);
return $config;
