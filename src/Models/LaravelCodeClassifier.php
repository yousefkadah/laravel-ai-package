<?php

namespace App\Models;

use Rubix\ML\Classifiers\ClassificationTree;
use Rubix\ML\Classifiers\MultilayerPerceptron;
use Rubix\ML\Classifiers\RandomForest;
use Rubix\ML\Classifiers\SVC;
use Rubix\ML\Datasets\Labeled;
use Rubix\ML\NeuralNet\Layers\Dense;
use Rubix\ML\NeuralNet\Layers\Activation;
use Rubix\ML\NeuralNet\ActivationFunctions\LeakyReLU;
use Rubix\ML\NeuralNet\Optimizers\Adam;
use Rubix\ML\Persisters\Filesystem;

/**
 * Base model for PHP and Laravel code classification.
 */
class LaravelCodeClassifier
{
    /**
     * The trained estimator instance.
     *
     * @var mixed
     */
    protected $estimator;

    /**
     * Available model types.
     *
     * @var array
     */
    protected $availableModels = [
        'random_forest' => RandomForest::class,
        'neural_network' => MultilayerPerceptron::class,
        'decision_tree' => ClassificationTree::class,
        'svm' => SVC::class,
    ];

    /**
     * Create a new model instance.
     *
     * @param string $modelType
     * @param array $params
     * @return void
     */
    public function __construct(string $modelType = 'random_forest', array $params = [])
    {
        if (!isset($this->availableModels[$modelType])) {
            throw new \InvalidArgumentException("Model type '{$modelType}' is not supported.");
        }

        $this->initializeModel($modelType, $params);
    }

    /**
     * Initialize the model based on the specified type.
     *
     * @param string $modelType
     * @param array $params
     * @return void
     */
    protected function initializeModel(string $modelType, array $params): void
    {
        switch ($modelType) {
            case 'random_forest':
                $this->estimator = new RandomForest(
                    null, // base learner (default decision tree)
                    $params['trees'] ?? 100, // number of estimators
                    $params['ratio'] ?? 0.2, // ratio of samples
                    $params['balanced'] ?? false // balanced sampling
                );
                break;

            case 'neural_network':
                $this->estimator = new MultilayerPerceptron([
                    new Dense($params['hidden_nodes'] ?? 100, $params['l2_penalty'] ?? 0.0, $params['bias'] ?? true),
                    new Activation(new LeakyReLU()),
                    new Dense($params['hidden_nodes_2'] ?? 50, $params['l2_penalty'] ?? 0.0, $params['bias'] ?? true),
                    new Activation(new LeakyReLU()),
                ], $params['batch_size'] ?? 128, new Adam($params['learning_rate'] ?? 0.001));
                break;

            case 'decision_tree':
                $this->estimator = new ClassificationTree(
                    $params['max_height'] ?? PHP_INT_MAX,
                    $params['max_leaf_size'] ?? 3,
                    $params['min_purity_increase'] ?? 1e-7,
                    $params['max_features'] ?? null,
                    $params['max_bins'] ?? null
                );
                break;

            case 'svm':
                $this->estimator = new SVC(
                    $params['c'] ?? 1.0,
                    $params['kernel'] ?? null,
                    $params['shrinking'] ?? true,
                    $params['tolerance'] ?? 1e-3,
                    $params['cache_size'] ?? 100.0
                );
                break;
        }
    }

    /**
     * Train the model with the given dataset.
     *
     * @param \Rubix\ML\Datasets\Labeled $dataset
     * @return void
     */
    public function train(Labeled $dataset): void
    {
        $this->estimator->train($dataset);
    }

    /**
     * Make predictions on the given samples.
     *
     * @param array $samples
     * @return array
     */
    public function predict(array $samples): array
    {
        return $this->estimator->predict($samples);
    }

    /**
     * Save the trained model to disk.
     *
     * @param string $path
     * @return void
     */
    public function save(string $path): void
    {
        $serializer = new \Rubix\ML\Serializers\Native();
        $encoding = $serializer->serialize($this->estimator);
        
        $persister = new Filesystem($path);
        $persister->save($encoding);
    }

    /**
     * Load a trained model from disk.
     *
     * @param string $path
     * @return void
     */
    public function load(string $path): void
    {
        $persister = new Filesystem($path);
        $this->estimator = $persister->load();
    }

    /**
     * Get the underlying estimator instance.
     *
     * @return mixed
     */
    public function getEstimator()
    {
        return $this->estimator;
    }
}
