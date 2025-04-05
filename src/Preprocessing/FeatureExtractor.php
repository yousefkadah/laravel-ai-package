<?php

namespace App\Preprocessing;

use Rubix\ML\Transformers\Transformer;
use Rubix\ML\DataType;

/**
 * Feature Extractor for PHP and Laravel code samples.
 */
class FeatureExtractor implements Transformer
{
    /**
     * Laravel-specific patterns to look for
     * 
     * @var array
     */
    protected $laravelPatterns = [
        'eloquent' => [
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
        'controller' => [
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
        'request' => [
            '/extends\s+formrequest/',
            '/public\s+function\s+rules/',
            '/public\s+function\s+authorize/',
            '/\$request->validate\(/',
            '/\$request->validated\(/',
        ],
        'service' => [
            '/namespace\s+app\\\\services/',
            '/class\s+\w+service/',
        ],
        'provider' => [
            '/extends\s+serviceprovider/',
            '/public\s+function\s+register/',
            '/public\s+function\s+boot/',
            '/\$this->app->singleton\(/',
            '/\$this->app->bind\(/',
        ],
    ];

    /**
     * Fit the transformer to a dataset.
     *
     * @param array $samples
     * @return void
     */
    public function fit(array $samples = []): void
    {
        // This transformer doesn't need to be fitted
    }

    /**
     * Transform the dataset in place.
     *
     * @param array $samples
     * @return void
     */
    public function transform(array &$samples): void
    {
        foreach ($samples as &$sample) {
            if (is_string($sample)) {
                $features = [];
                
                // Extract basic code metrics
                $features['length'] = strlen($sample);
                $features['line_count'] = substr_count($sample, "\n") + 1;
                
                // Count PHP language constructs
                $features['class_count'] = preg_match_all('/class\s+\w+/', $sample, $matches);
                $features['function_count'] = preg_match_all('/function\s+\w+/', $sample, $matches);
                $features['namespace_count'] = preg_match_all('/namespace\s+[\w\\\\]+/', $sample, $matches);
                $features['use_count'] = preg_match_all('/use\s+[\w\\\\]+/', $sample, $matches);
                
                // Extract Laravel-specific features
                foreach ($this->laravelPatterns as $category => $patterns) {
                    $categoryScore = 0;
                    foreach ($patterns as $pattern) {
                        $matches = preg_match_all($pattern, $sample, $m);
                        $categoryScore += $matches;
                    }
                    $features[$category . '_score'] = $categoryScore;
                }
                
                // Replace the original code with the extracted features
                $sample = json_encode($features);
            }
        }
    }

    /**
     * Return the data types that this transformer is compatible with.
     *
     * @return \Rubix\ML\DataType[]
     */
    public function compatibility(): array
    {
        return [
            DataType::string(),
        ];
    }

    /**
     * Return the string representation of the transformer.
     *
     * @return string
     */
    public function __toString(): string
    {
        return 'Feature Extractor';
    }
}
