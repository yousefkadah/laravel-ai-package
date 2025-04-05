<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Laravel AI Model Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration options for the Laravel AI model
    | that specializes in PHP and Laravel code classification.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Model Path
    |--------------------------------------------------------------------------
    |
    | Path to the trained model file. If the model file exists, it will be
    | loaded automatically. If not, a new model will be created using the
    | specified model type and parameters.
    |
    */
    'model_path' => storage_path('app/models/laravel_classifier.model'),

    /*
    |--------------------------------------------------------------------------
    | Model Type
    |--------------------------------------------------------------------------
    |
    | The type of model to use when creating a new classifier. Available options:
    | - random_forest (default): Good general-purpose classifier
    | - neural_network: Better for complex patterns but requires more data
    | - decision_tree: Simpler model, good for interpretability
    | - svm: Good for high-dimensional data with clear margins
    |
    */
    'model_type' => 'random_forest',

    /*
    |--------------------------------------------------------------------------
    | Model Parameters
    |--------------------------------------------------------------------------
    |
    | Parameters for model initialization. These vary depending on the model type.
    |
    | Random Forest parameters:
    | - trees: Number of decision trees in the forest (default: 100)
    | - ratio: Ratio of samples to use for training each tree (default: 0.2)
    | - balanced: Whether to use balanced sampling (default: false)
    |
    | Neural Network parameters:
    | - hidden_nodes: Number of neurons in first hidden layer (default: 100)
    | - hidden_nodes_2: Number of neurons in second hidden layer (default: 50)
    | - batch_size: Batch size for training (default: 128)
    | - learning_rate: Learning rate for optimizer (default: 0.001)
    |
    | Decision Tree parameters:
    | - max_height: Maximum height of the tree (default: PHP_INT_MAX)
    | - max_leaf_size: Maximum number of samples in a leaf node (default: 3)
    | - min_purity_increase: Minimum increase in purity to split (default: 1e-7)
    |
    | SVM parameters:
    | - c: C parameter for SVM (default: 1.0)
    | - kernel: Kernel type (default: null, uses RBF)
    | - shrinking: Whether to use the shrinking heuristic (default: true)
    |
    */
    'model_params' => [
        'trees' => 100,
        'ratio' => 0.2,
        'balanced' => false,
    ],
];
