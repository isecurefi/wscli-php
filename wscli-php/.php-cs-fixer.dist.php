<?php

declare(strict_types=1);

$finder = (new PhpCsFixer\Finder())
    ->in([__DIR__ . '/src', __DIR__ . '/tests'])
    ->exclude(['vendor', 'build', '.phpunit.cache']);

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(true)
    ->setRules([
        '@PER-CS2.0'       => true,
        '@PHP83Migration'  => true,
        'declare_strict_types' => true,
        'native_function_invocation' => false,
        'ordered_imports'  => ['sort_algorithm' => 'alpha'],
    ])
    ->setFinder($finder);
