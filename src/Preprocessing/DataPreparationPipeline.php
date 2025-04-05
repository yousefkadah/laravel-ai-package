<?php

namespace App\Preprocessing;

use DirectoryIterator;
use Rubix\ML\Datasets\Labeled;

/**
 * Data Preparation Pipeline for PHP and Laravel code samples.
 */
class DataPreparationPipeline
{
    /**
     * Source directory for raw code samples
     * 
     * @var string
     */
    protected $sourceDir;
    
    /**
     * Destination directory for processed data
     * 
     * @var string
     */
    protected $destDir;
    
    /**
     * Array of transformers to apply
     * 
     * @var array
     */
    protected $transformers;
    
    /**
     * Create a new data preparation pipeline instance.
     *
     * @param string $sourceDir
     * @param string $destDir
     * @param array $transformers
     * @return void
     */
    public function __construct(string $sourceDir, string $destDir, array $transformers = [])
    {
        $this->sourceDir = $sourceDir;
        $this->destDir = $destDir;
        $this->transformers = $transformers;
    }
    
    /**
     * Process the data and create training and testing datasets.
     *
     * @param float $testRatio
     * @return array
     */
    public function process(float $testRatio = 0.2): array
    {
        // Load raw code samples
        $samples = [];
        $labels = [];
        
        foreach (new DirectoryIterator($this->sourceDir) as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $code = file_get_contents($file->getPathname());
                $samples[] = $code;
                
                // Determine label based on filename or content
                $filename = $file->getFilename();
                if (strpos($filename, 'Controller') !== false) {
                    $labels[] = 'controller';
                } elseif (strpos($filename, 'Service') !== false) {
                    $labels[] = 'service';
                } elseif (strpos($filename, 'Provider') !== false) {
                    $labels[] = 'provider';
                } elseif (strpos($filename, 'Request') !== false) {
                    $labels[] = 'request';
                } else {
                    $labels[] = 'model';
                }
            }
        }
        
        // Apply transformers
        foreach ($this->transformers as $transformer) {
            $transformer->transform($samples);
        }
        
        // Create dataset
        $dataset = new Labeled($samples, $labels);
        
        // Split into training and testing sets
        [$training, $testing] = $dataset->split($testRatio);
        
        // Save processed datasets
        file_put_contents(
            $this->destDir . '/training.json', 
            json_encode([
                'samples' => $training->samples(),
                'labels' => $training->labels(),
            ])
        );
        
        file_put_contents(
            $this->destDir . '/testing.json', 
            json_encode([
                'samples' => $testing->samples(),
                'labels' => $testing->labels(),
            ])
        );
        
        return [$training, $testing];
    }
}
