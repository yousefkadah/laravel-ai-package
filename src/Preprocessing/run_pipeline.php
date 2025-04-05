<?php

namespace App\Preprocessing;

/**
 * Script to run the data preparation pipeline.
 */

require_once __DIR__ . '/../../vendor/autoload.php';

use App\Preprocessing\PhpCodeTokenizer;
use App\Preprocessing\CodeNormalizer;
use App\Preprocessing\FeatureExtractor;
use App\Preprocessing\DataPreparationPipeline;

// Define directories
$sourceDir = __DIR__ . '/../../data/raw';
$destDir = __DIR__ . '/../../data/processed';

// Create destination directory if it doesn't exist
if (!is_dir($destDir)) {
    mkdir($destDir, 0755, true);
}

// Create transformers
$tokenizer = new PhpCodeTokenizer();
$normalizer = new CodeNormalizer();
$featureExtractor = new FeatureExtractor();

// Create and run the pipeline
$pipeline = new DataPreparationPipeline(
    $sourceDir,
    $destDir,
    [$normalizer, $featureExtractor]
);

echo "Starting data preparation pipeline...\n";
[$training, $testing] = $pipeline->process(0.2);

echo "Data preparation complete.\n";
echo "Training samples: " . count($training->samples()) . "\n";
echo "Testing samples: " . count($testing->samples()) . "\n";
echo "Processed data saved to: $destDir\n";
