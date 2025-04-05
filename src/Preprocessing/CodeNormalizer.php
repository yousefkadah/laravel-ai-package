<?php

namespace App\Preprocessing;

use Rubix\ML\Transformers\Transformer;
use Rubix\ML\DataType;

/**
 * Code Normalizer for preprocessing PHP and Laravel code samples.
 */
class CodeNormalizer implements Transformer
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
            if (is_string($sample)) {
                // Remove comments
                $sample = preg_replace('/(\/\/.*|\/\*[\s\S]*?\*\/)/', '', $sample);
                
                // Normalize whitespace
                $sample = preg_replace('/\s+/', ' ', $sample);
                
                // Convert to lowercase for case insensitivity
                $sample = strtolower($sample);
                
                // Remove unnecessary characters
                $sample = preg_replace('/[^\w\s\$\{\}\(\)\[\]\-\>\:\;\.\,\=\+\*\/\!\?\|\&\%\^\<\>]/', '', $sample);
                
                // Trim
                $sample = trim($sample);
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
        return 'Code Normalizer';
    }
}
