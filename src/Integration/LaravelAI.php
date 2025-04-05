<?php

namespace App\Integration;

use App\Models\LaravelCodeClassifier;
use App\Models\ModelFactory;

/**
 * Laravel integration for PHP and Laravel code classifier.
 */
class LaravelAI
{
    /**
     * The classifier instance.
     *
     * @var \App\Models\LaravelCodeClassifier
     */
    protected $classifier;

    /**
     * Create a new LaravelAI instance.
     *
     * @param \App\Models\LaravelCodeClassifier|null $classifier
     * @return void
     */
    public function __construct(?LaravelCodeClassifier $classifier = null)
    {
        $this->classifier = $classifier;
    }

    /**
     * Load a trained model from disk.
     *
     * @param string $path
     * @return self
     */
    public function loadModel(string $path): self
    {
        $this->classifier = ModelFactory::load($path);
        return $this;
    }

    /**
     * Create a new model with the specified type.
     *
     * @param string $modelType
     * @param array $params
     * @return self
     */
    public function createModel(string $modelType = 'random_forest', array $params = []): self
    {
        $this->classifier = ModelFactory::create($modelType, $params);
        return $this;
    }

    /**
     * Classify PHP or Laravel code.
     *
     * @param string $code
     * @return string
     */
    public function classifyCode(string $code): string
    {
        if (!$this->classifier) {
            throw new \RuntimeException('No classifier has been loaded or created.');
        }

        // Preprocess the code (similar to what we did in the preprocessing pipeline)
        $sample = $this->preprocessCode($code);
        
        // Make prediction
        $predictions = $this->classifier->predict([$sample]);
        
        return $predictions[0] ?? 'unknown';
    }

    /**
     * Classify multiple code samples.
     *
     * @param array $codeSamples
     * @return array
     */
    public function classifyBatch(array $codeSamples): array
    {
        if (!$this->classifier) {
            throw new \RuntimeException('No classifier has been loaded or created.');
        }

        // Preprocess each code sample
        $samples = array_map([$this, 'preprocessCode'], $codeSamples);
        
        // Make predictions
        return $this->classifier->predict($samples);
    }

    /**
     * Preprocess code for classification.
     *
     * @param string $code
     * @return string
     */
    protected function preprocessCode(string $code): string
    {
        // Remove comments
        $code = preg_replace('/(\/\/.*|\/\*[\s\S]*?\*\/)/', '', $code);
        
        // Normalize whitespace
        $code = preg_replace('/\s+/', ' ', $code);
        
        // Convert to lowercase for case insensitivity
        $code = strtolower($code);
        
        // Remove unnecessary characters
        $code = preg_replace('/[^\w\s\$\{\}\(\)\[\]\-\>\:\;\.\,\=\+\*\/\!\?\|\&\%\^\<\>]/', '', $code);
        
        // Trim
        $code = trim($code);
        
        // Extract features (similar to FeatureExtractor)
        $features = [];
        
        // Extract basic code metrics
        $features['length'] = strlen($code);
        $features['line_count'] = substr_count($code, "\n") + 1;
        
        // Count PHP language constructs
        $features['class_count'] = preg_match_all('/class\s+\w+/', $code, $matches);
        $features['function_count'] = preg_match_all('/function\s+\w+/', $code, $matches);
        $features['namespace_count'] = preg_match_all('/namespace\s+[\w\\\\]+/', $code, $matches);
        $features['use_count'] = preg_match_all('/use\s+[\w\\\\]+/', $code, $matches);
        
        // Extract Laravel-specific features
        $laravelPatterns = [
            'eloquent_score' => [
                '/\$table\s*=/',
                '/\$fillable\s*=/',
                '/\$guarded\s*=/',
                '/\$casts\s*=/',
                '/\$with\s*=/',
                '/\$appends\s*=/',
                '/\$hidden\s*=/',
                '/\$timestamps\s*=/',
                '/belongsto\(/',
                '/hasmany\(/',
                '/hasone\(/',
                '/belongstomany\(/',
                '/wherehas\(/',
                '/orderby\(/',
                '/groupby\(/',
                '/join\(/',
            ],
            'controller_score' => [
                '/extends\s+controller/',
                '/public\s+function\s+index/',
                '/public\s+function\s+show/',
                '/public\s+function\s+store/',
                '/public\s+function\s+update/',
                '/public\s+function\s+destroy/',
                '/return\s+view\(/',
                '/return\s+redirect\(/',
                '/return\s+response\(/',
                '/return\s+json\(/',
            ],
            'request_score' => [
                '/extends\s+formrequest/',
                '/public\s+function\s+rules/',
                '/public\s+function\s+authorize/',
                '/\$request->validate\(/',
                '/\$request->validated\(/',
            ],
            'service_score' => [
                '/namespace\s+app\\\\services/',
                '/class\s+\w+service/',
            ],
            'provider_score' => [
                '/extends\s+serviceprovider/',
                '/public\s+function\s+register/',
                '/public\s+function\s+boot/',
                '/\$this->app->singleton\(/',
                '/\$this->app->bind\(/',
            ],
        ];
        
        foreach ($laravelPatterns as $category => $patterns) {
            $categoryScore = 0;
            foreach ($patterns as $pattern) {
                $matches = preg_match_all($pattern, $code, $m);
                $categoryScore += $matches;
            }
            $features[$category] = $categoryScore;
        }
        
        return json_encode($features);
    }

    /**
     * Get the underlying classifier instance.
     *
     * @return \App\Models\LaravelCodeClassifier|null
     */
    public function getClassifier(): ?LaravelCodeClassifier
    {
        return $this->classifier;
    }
}
