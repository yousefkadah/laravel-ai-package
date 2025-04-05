<?php

namespace App\Models;

use Rubix\ML\Datasets\Labeled;
use Rubix\ML\Persisters\Filesystem;

/**
 * PHP and Laravel code classifier factory.
 */
class ModelFactory
{
    /**
     * Create a new classifier instance with the specified model type.
     *
     * @param string $modelType
     * @param array $params
     * @return \App\Models\LaravelCodeClassifier
     */
    public static function create(string $modelType = 'random_forest', array $params = []): LaravelCodeClassifier
    {
        return new LaravelCodeClassifier($modelType, $params);
    }

    /**
     * Load a trained model from disk.
     *
     * @param string $path
     * @return \App\Models\LaravelCodeClassifier
     */
    public static function load(string $path): LaravelCodeClassifier
    {
        $classifier = new LaravelCodeClassifier();
        $classifier->load($path);
        return $classifier;
    }

    /**
     * Create a dataset from JSON file.
     *
     * @param string $path
     * @return \Rubix\ML\Datasets\Labeled
     */
    public static function createDatasetFromJson(string $path): Labeled
    {
        $data = json_decode(file_get_contents($path), true);
        
        if (!isset($data['samples']) || !isset($data['labels'])) {
            throw new \InvalidArgumentException("Invalid dataset format in {$path}");
        }
        
        // Convert JSON string samples back to arrays if needed
        $samples = array_map(function ($sample) {
            if (is_string($sample) && $decoded = json_decode($sample, true)) {
                return $decoded;
            }
            return $sample;
        }, $data['samples']);
        
        return new Labeled($samples, $data['labels']);
    }
}
