<?php

namespace App\Dataset;

use Rubix\ML\Datasets\Dataset;
use Rubix\ML\Datasets\Labeled;

/**
 * Dataset handler for PHP and Laravel code samples.
 */
class LaravelDataset
{
    /**
     * Load a dataset from a file.
     *
     * @param string $path
     * @return \Rubix\ML\Datasets\Dataset
     */
    public static function fromFile(string $path): Dataset
    {
        // Implementation will be added in the data collection phase
        throw new \RuntimeException('Method not implemented yet');
    }

    /**
     * Create a labeled dataset from samples and labels.
     *
     * @param array $samples
     * @param array $labels
     * @return \Rubix\ML\Datasets\Labeled
     */
    public static function create(array $samples, array $labels): Labeled
    {
        return new Labeled($samples, $labels);
    }

    /**
     * Split a dataset into training and testing sets.
     *
     * @param \Rubix\ML\Datasets\Labeled $dataset
     * @param float $ratio
     * @return array
     */
    public static function split(Labeled $dataset, float $ratio = 0.8): array
    {
        return $dataset->split($ratio);
    }
}
