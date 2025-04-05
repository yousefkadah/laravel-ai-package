<?php

namespace App\Preprocessing;

use Rubix\ML\Transformers\Transformer;

/**
 * Base preprocessor for PHP and Laravel code samples.
 */
class CodePreprocessor implements Transformer
{
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
            // Basic preprocessing for code samples
            if (is_string($sample)) {
                // Remove comments
                $sample = preg_replace('/(\/\/.*|\/\*[\s\S]*?\*\/)/', '', $sample);
                
                // Normalize whitespace
                $sample = preg_replace('/\s+/', ' ', $sample);
                
                // Trim
                $sample = trim($sample);
            }
        }
    }

    /**
     * Return the string representation of the transformer.
     *
     * @return string
     */
    public function __toString(): string
    {
        return 'Code Preprocessor';
    }
}
