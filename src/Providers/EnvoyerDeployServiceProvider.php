<?php

namespace Log1x\EnvoyerDeploy\Providers;

use Illuminate\Support\ServiceProvider;
use Log1x\EnvoyerDeploy\Console\DeployCommand;
use Log1x\EnvoyerDeploy\Console\DeployListCommand;

class EnvoyerDeployServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../../config/envoyer.php' => $this->app->configPath('envoyer.php'),
        ], 'envoyer-deploy-config');

        $this->mergeConfigFrom(
            __DIR__.'/../../config/envoyer.php', 'envoyer'
        );

        $this->commands([
            DeployCommand::class,
            DeployListCommand::class,
        ]);
    }
}
