# PHP and Laravel AI Model Documentation

## Project Overview

This project implements a specialized AI model for PHP and Laravel using RubixML, a machine learning library built specifically for PHP. The model is designed to classify PHP code into different categories based on Laravel components (models, controllers, services, etc.) and can be easily integrated into Laravel applications.

## Model Architecture

### Core Components

1. **Data Collection and Preprocessing**
   - Raw PHP and Laravel code samples are collected from various sources
   - Code is tokenized, normalized, and cleaned
   - Features are extracted from the code samples
   - Data is split into training and testing sets

2. **Model Implementation**
   - Multiple model types are supported:
     - Random Forest (default): Good general-purpose classifier
     - Neural Network: Better for complex patterns but requires more data
     - Decision Tree: Simpler model, good for interpretability
     - SVM: Good for high-dimensional data with clear margins
   - Models are trained on preprocessed data
   - Trained models can be saved to disk for later use

3. **Laravel Integration**
   - LaravelAI class provides a clean interface for using the model
   - LaravelAIServiceProvider integrates with Laravel's service container
   - LaravelAIFacade provides easy access to the functionality
   - Configuration file allows customization of model parameters

## Directory Structure

```
php-laravel-ai/
├── config/                  # Configuration files
│   └── laravel-ai.php       # Laravel AI configuration
├── data/                    # Data directory
│   ├── models/              # Saved models
│   ├── processed/           # Processed data
│   └── raw/                 # Raw code samples
├── src/                     # Source code
│   ├── Dataset/             # Dataset handling classes
│   ├── Examples/            # Usage examples
│   ├── Integration/         # Laravel integration components
│   ├── Models/              # Model implementation
│   ├── Preprocessing/       # Data preprocessing utilities
│   └── Utils/               # Utility functions
├── tests/                   # Test files
├── composer.json            # Composer configuration
└── README.md                # Project README
```

## Installation

### Requirements

- PHP 7.4 or higher
- Composer
- Laravel (for integration)

### Installation Steps

1. Install the package via Composer:

```bash
composer require user/php-laravel-ai
```

2. If you're using Laravel, publish the configuration file:

```bash
php artisan vendor:publish --provider="App\Integration\LaravelAIServiceProvider"
```

3. Configure the model in `config/laravel-ai.php`:

```php
return [
    'model_path' => storage_path('app/models/laravel_classifier.model'),
    'model_type' => 'random_forest',
    'model_params' => [
        'trees' => 100,
        'ratio' => 0.2,
        'balanced' => false,
    ],
];
```

## Usage

### Standalone Usage

```php
use App\Integration\LaravelAI;

// Create a new instance of LaravelAI
$laravelAI = new LaravelAI();

// Load a trained model
$laravelAI->loadModel('/path/to/model.model');

// Classify a code sample
$code = file_get_contents('path/to/code.php');
$classification = $laravelAI->classifyCode($code);

echo "Code classification: " . $classification;
```

### Laravel Integration

In a Laravel application, you can use the facade:

```php
use LaravelAI;

// Classify a code sample
$code = file_get_contents('path/to/code.php');
$classification = LaravelAI::classifyCode($code);

echo "Code classification: " . $classification;
```

### Batch Processing

```php
use App\Integration\LaravelAI;

// Create a new instance of LaravelAI
$laravelAI = new LaravelAI();

// Load a trained model
$laravelAI->loadModel('/path/to/model.model');

// Prepare code samples
$samples = [
    file_get_contents('path/to/code1.php'),
    file_get_contents('path/to/code2.php'),
    file_get_contents('path/to/code3.php'),
];

// Classify the code samples
$classifications = $laravelAI->classifyBatch($samples);

foreach ($classifications as $index => $classification) {
    echo "Sample " . ($index + 1) . " classification: " . $classification . PHP_EOL;
}
```

## Training Your Own Model

If you want to train your own model with custom data:

1. Collect PHP and Laravel code samples and place them in the `data/raw` directory
2. Run the preprocessing pipeline:

```bash
php src/Preprocessing/run_pipeline.php
```

3. Train the model:

```bash
php src/Models/train.php
```

4. The trained model will be saved to `data/models/laravel_classifier.model`

## Model Performance

The model's performance depends on the quality and quantity of the training data. With the provided sample data, the model achieves:

- Accuracy: 25.00%
- F1 Score: 10.00%

These metrics are expected to improve significantly with a larger dataset.

## Customization

### Model Parameters

You can customize the model parameters in the configuration file:

```php
'model_params' => [
    // Random Forest parameters
    'trees' => 100,
    'ratio' => 0.2,
    'balanced' => false,
    
    // Neural Network parameters
    'hidden_nodes' => 100,
    'hidden_nodes_2' => 50,
    'batch_size' => 128,
    'learning_rate' => 0.001,
    
    // Decision Tree parameters
    'max_height' => PHP_INT_MAX,
    'max_leaf_size' => 3,
    'min_purity_increase' => 1e-7,
    
    // SVM parameters
    'c' => 1.0,
    'kernel' => null,
    'shrinking' => true,
],
```

### Adding New Features

To add new features to the model:

1. Modify the `FeatureExtractor.php` file to extract additional features
2. Update the `preprocessCode` method in `LaravelAI.php` to match the new features
3. Retrain the model with the updated feature extraction

## Troubleshooting

### Common Issues

1. **Model Not Found**: Ensure the model path in the configuration is correct and the model file exists
2. **Low Accuracy**: Try training with more data or adjusting the model parameters
3. **Memory Issues**: For large datasets, try reducing the number of trees in Random Forest or batch size in Neural Network

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Credits

- RubixML: [https://rubixml.com/](https://rubixml.com/)
- Laravel: [https://laravel.com/](https://laravel.com/)
