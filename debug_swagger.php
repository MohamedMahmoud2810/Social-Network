<?php

/**
 * Debug Swagger generation issue
 */

use OpenApi\Generator;

require 'vendor/autoload.php';

$openapi = Generator::scan(
    [base_path('app')],
    [
        'processors' => [
            new \OpenApi\Processors\CleanDuplicateOperation,
        ],
    ]
);

// Try to validate
try {
    $openapi->validate();
    echo "Validation passed!\n";
    echo 'Total paths: '.count($openapi->paths)."\n";
} catch (\Exception $e) {
    echo 'Validation failed: '.$e->getMessage()."\n";

    // Print all paths found
    if ($openapi->paths) {
        echo "\nPaths found:\n";
        foreach ($openapi->paths as $path => $pathItem) {
            echo '  Path: '.$path."\n";
            if ($pathItem->get) {
                echo "    - GET\n";
            }
            if ($pathItem->post) {
                echo "    - POST\n";
            }
            if ($pathItem->put) {
                echo "    - PUT\n";
            }
            if ($pathItem->delete) {
                echo "    - DELETE\n";
            }
            if ($pathItem->patch) {
                echo "    - PATCH\n";
            }
        }
    } else {
        echo "\nNo paths found!\n";
    }

    // Print OpenAPI info
    if ($openapi->info) {
        echo "\nInfo found: ".$openapi->info->title."\n";
    }
}
