<?php

namespace App\Models;

use Rubix\ML\Datasets\Labeled;
use Rubix\ML\CrossValidation\Reports\ConfusionMatrix;
use Rubix\ML\CrossValidation\Reports\MulticlassBreakdown;

/**
 * Training script for PHP and Laravel code classifier.
 */
require_once __DIR__ . '/../../vendor/autoload.php';

use App\Models\ModelFactory;
use App\Models\LaravelCodeClassifier;
use App\Models\ModelEvaluator;

// Define paths
$trainingDataPath = __DIR__ . '/../../data/processed/training.json';
$testingDataPath = __DIR__ . '/../../data/processed/testing.json';
$modelSavePath = __DIR__ . '/../../data/models/laravel_classifier.model';

// Ensure model directory exists
if (!is_dir(dirname($modelSavePath))) {
    mkdir(dirname($modelSavePath), 0755, true);
}

// Load datasets
echo "Loading datasets...\n";
$trainingSet = ModelFactory::createDatasetFromJson($trainingDataPath);
$testingSet = ModelFactory::createDatasetFromJson($testingDataPath);

echo "Training set: " . $trainingSet->numSamples() . " samples\n";
echo "Testing set: " . $testingSet->numSamples() . " samples\n";

// Create model
echo "Creating model...\n";
$modelType = 'random_forest'; // Options: random_forest, neural_network, decision_tree, svm
$params = [
    'trees' => 100,
    'max_depth' => 20,
];
$classifier = ModelFactory::create($modelType, $params);

// Train model
echo "Training model...\n";
$classifier->train($trainingSet);

// Save model
echo "Saving model to {$modelSavePath}...\n";
$classifier->save($modelSavePath);

// Evaluate model
echo "Evaluating model...\n";
$evaluator = new ModelEvaluator($classifier);
$results = $evaluator->evaluate($testingSet);

// Display results
echo "Evaluation Results:\n";
echo "Accuracy: " . number_format($results['accuracy'] * 100, 2) . "%\n";
echo "F1 Score: " . number_format($results['f1_score'] * 100, 2) . "%\n";
echo "Confusion Matrix:\n";
print_r($results['confusion_matrix']);

// Cross-validation (if dataset is large enough)
if ($trainingSet->numSamples() >= 10) {
    echo "Performing cross-validation...\n";
    $cvResults = $evaluator->crossValidate($trainingSet, 5);
    
    echo "Cross-Validation Results:\n";
    echo "Mean Accuracy: " . number_format($cvResults['accuracy']['mean'] * 100, 2) . "%\n";
    echo "Accuracy Std Dev: " . number_format($cvResults['accuracy']['std'] * 100, 2) . "%\n";
    echo "Mean F1 Score: " . number_format($cvResults['f1_score']['mean'] * 100, 2) . "%\n";
    echo "F1 Score Std Dev: " . number_format($cvResults['f1_score']['std'] * 100, 2) . "%\n";
} else {
    echo "Dataset too small for meaningful cross-validation.\n";
}

echo "Model training and evaluation complete.\n";
