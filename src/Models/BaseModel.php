<?php

namespace App\Models;

use Rubix\ML\Estimator;
use Rubix\ML\Persistable;

/**
 * Base model class for PHP and Laravel AI models.
 */
abstract class BaseModel implements Persistable
{
    /**
     * The underlying estimator instance.
     *
     * @var \Rubix\ML\Estimator
     */
    protected $estimator;

    /**
     * Create a new model instance.
     *
     * @param \Rubix\ML\Estimator $estimator
     * @return void
     */
    public function __construct(Estimator $estimator)
    {
        $this->estimator = $estimator;
    }

    /**
     * Return the estimator instance.
     *
     * @return \Rubix\ML\Estimator
     */
    public function estimator(): Estimator
    {
        return $this->estimator;
    }

    /**
     * Make predictions from samples.
     *
     * @param array $samples
     * @return array
     */
    public function predict(array $samples): array
    {
        return $this->estimator->predict($samples);
    }
}
