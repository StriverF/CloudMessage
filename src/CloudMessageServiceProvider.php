<?php

namespace PatPat\CloudMessage;

use Illuminate\Support\ServiceProvider;

class DingDingServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //publish
        $this->publishes([
            __DIR__.'/config/cloud_message.php' => config_path('cloud_message.php'),
        ]);

    }


    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array();
    }
}
