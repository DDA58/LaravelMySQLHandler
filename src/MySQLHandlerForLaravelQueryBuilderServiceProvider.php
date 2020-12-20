<?php

namespace DDA58\MySQLHandlerForLaravelQueryBuilder;

use Illuminate\Support\ServiceProvider;

use \DDA58\MySQLHandlerForLaravelQueryBuilder\MySQLHandlerForLaravelQueryBuilder;
use \DDA58\MySQLHandlerForLaravelQueryBuilder\Contracts\MySQLHandlerRepositoryContract;
use \DDA58\MySQLHandlerForLaravelQueryBuilder\MySQLHandlerRepository;

/**
* class MySQLHandlerForLaravelQueryBuilderServiceProvider
* @package DDA58\MySQLHandlerForLaravelQueryBuilder
*/
class MySQLHandlerForLaravelQueryBuilderServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot(MySQLHandlerForLaravelQueryBuilder $handler): void
    {
        $handler->initHandlerMethodsForQueryBuilder();
        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }

    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton(MySQLHandlerRepositoryContract::class, function ($app) {
            return $app->make(MySQLHandlerRepository::class);
        });

        $this->app->singleton(MySQLHandlerForLaravelQueryBuilder::class, function ($app) {
            return new MySQLHandlerForLaravelQueryBuilder($app->make(MySQLHandlerRepositoryContract::class));
        });

        // Register the service the package provides.
        $this->app->singleton('mysqlhandlerforlaravelquerybuilder', function ($app) {
            return new MySQLHandlerForLaravelQueryBuilder;
        });
    }

    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole(): void
    {
        // Publishing the configuration file;
    }
}
