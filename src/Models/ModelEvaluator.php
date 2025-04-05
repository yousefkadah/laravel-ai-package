<?php

namespace App\Models;

use Rubix\ML\CrossValidation\Metrics\Accuracy;
use Rubix\ML\CrossValidation\Metrics\FBeta;
use Rubix\ML\CrossValidation\Reports\ConfusionMatrix;
use Rubix\ML\Datasets\Labeled;
use Rubix\ML\Datasets\Unlabeled;

/**
 * Model evaluator for PHP and Laravel code classification.
 */
class ModelEvaluator
{
    /**
     * The classifier instance.
     *
     * @var \App\Models\LaravelCodeClassifier
     */
    protected $classifier;

    /**
     * Create a new evaluator instance.
     *
     * @param \App\Models\LaravelCodeClassifier $classifier
     * @return void
     */
    public function __construct(LaravelCodeClassifier $classifier)
    {
        $this->classifier = $classifier;
    }

    /**
     * Evaluate the model using the given testing dataset.
     *
     * @param \Rubix\ML\Datasets\Labeled $testingSet
     * @return array
     */
    public function evaluate(Labeled $testingSet): array
    {
        $estimator = $this->classifier->getEstimator();
        
        // Make predictions
        $predictions = $estimator->predict($testingSet);
        
        // Calculate metrics
        $accuracy = new Accuracy();
        $accuracyScore = $accuracy->score($predictions, $testingSet->labels());
        
        $f1 = new FBeta(1.0);
        $f1Score = $f1->score($predictions, $testingSet->labels());
        
        $confusionMatrix = new ConfusionMatrix();
        $matrix = $confusionMatrix->generate($predictions, $testingSet->labels());
        
        return [
            'accuracy' => $accuracyScore,
            'f1_score' => $f1Score,
            'confusion_matrix' => $matrix,
            'predictions' => $predictions,
            'actual' => $testingSet->labels(),
        ];
    }

    /**
     * Perform cross-validation on the model.
     *
     * @param \Rubix\ML\Datasets\Labeled $dataset
     * @param int $folds
     * @return array
     */
    public function crossValidate(Labeled $dataset, int $folds = 5): array
    {
        $estimator = $this->classifier->getEstimator();
        
        $accuracy = new Accuracy();
        $f1 = new FBeta(1.0);
        
        $accuracyScores = [];
        $f1Scores = [];
        
        // Simple k-fold cross-validation
        $foldSize = (int) floor($dataset->numSamples() / $folds);
        
        for ($i = 0; $i < $folds; $i++) {
            $testingOffset = $i * $foldSize;
            $testingLimit = $foldSize;
            
            // Handle last fold which might be larger
            if ($i === $folds - 1) {
                $testingLimit = $dataset->numSamples() - $testingOffset;
            }
            
            // Split dataset into training and testing sets
            $testing = $dataset->splice($testingOffset, $testingLimit);
            $training = $dataset;
            
            // Train the model
            $estimator->train($training);
            
            // Make predictions
            $predictions = $estimator->predict($testing);
            
            // Calculate metrics
            $accuracyScores[] = $accuracy->score($predictions, $testing->labels());
            $f1Scores[] = $f1->score($predictions, $testing->labels());
        }
        
        return [
            'accuracy' => [
                'scores' => $accuracyScores,
                'mean' => array_sum($accuracyScores) / count($accuracyScores),
                'std' => $this->standardDeviation($accuracyScores),
            ],
            'f1_score' => [
                'scores' => $f1Scores,
                'mean' => array_sum($f1Scores) / count($f1Scores),
                'std' => $this->standardDeviation($f1Scores),
            ],
        ];
    }
    
    /**
     * Calculate the standard deviation of an array of values.
     *
     * @param array $values
     * @return float
     */
    protected function standardDeviation(array $values): float
    {
        $mean = array_sum($values) / count($values);
        $variance = 0.0;
        
        foreach ($values as $value) {
            $variance += pow($value - $mean, 2);
        }
        
        return sqrt($variance / count($values));
    }
}
