<?php

namespace App\Integration;

use App\Models\BaseModel;

/**
 * Laravel integration for AI models.
 */
class LaravelIntegration
{
    /**
     * The model instance.
     *
     * @var \App\Models\BaseModel
     */
    protected $model;

    /**
     * Create a new integration instance.
     *
     * @param \App\Models\BaseModel $model
     * @return void
     */
    public function __construct(BaseModel $model)
    {
        $this->model = $model;
    }

    /**
     * Register the model with Laravel's service container.
     *
     * @param \Illuminate\Foundation\Application $app
     * @return void
     */
    public function register($app): void
    {
        $app->singleton(static::class, function ($app) {
            return $this;
        });
    }

    /**
     * Make predictions using the model.
     *
     * @param array $input
     * @return array
     */
    public function predict(array $input): array
    {
        return $this->model->predict($input);
    }
}
