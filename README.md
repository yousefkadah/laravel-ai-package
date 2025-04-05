# PHP and Laravel AI Model with RubixML

A specialized AI model for PHP and Laravel development assistance using RubixML, a machine learning library built specifically for PHP.

## Features

- **Code Classification**: Automatically classify PHP code into different Laravel components (models, controllers, services, etc.)
- **Multiple Model Types**: Support for Random Forest, Neural Network, Decision Tree, and SVM algorithms
- **Laravel Integration**: Easy integration with Laravel applications through service provider and facade
- **Customizable**: Configurable model parameters and preprocessing options
- **Extensible**: Framework for training custom models with your own data

## Requirements

- PHP 7.4 or higher
- Composer
- Laravel (for integration)

## Installation

### Via Composer

```bash
composer require user/php-laravel-ai
```

### Laravel Integration

If you're using Laravel, publish the configuration file:

```bash
php artisan vendor:publish --provider="App\Integration\LaravelAIServiceProvider"
```

## Quick Start

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

## Documentation

For detailed documentation, see the [documentation.md](docs/documentation.md) file.

## Project Structure

```
php-laravel-ai/
├── config/                  # Configuration files
│   └── laravel-ai.php       # Laravel AI configuration
├── data/                    # Data directory
│   ├── models/              # Saved models
│   ├── processed/           # Processed data
│   └── raw/                 # Raw code samples
├── docs/                    # Documentation
│   └── documentation.md     # Comprehensive documentation
├── src/                     # Source code
│   ├── Dataset/             # Dataset handling classes
│   ├── Examples/            # Usage examples
│   ├── Integration/         # Laravel integration components
│   ├── Models/              # Model implementation
│   ├── Preprocessing/       # Data preprocessing utilities
│   └── Utils/               # Utility functions
├── tests/                   # Test files
├── composer.json            # Composer configuration
└── README.md                # This file
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

## License

This project is licensed under the MIT License.

## Credits

- RubixML: [https://rubixml.com/](https://rubixml.com/)
- Laravel: [https://laravel.com/](https://laravel.com/)
