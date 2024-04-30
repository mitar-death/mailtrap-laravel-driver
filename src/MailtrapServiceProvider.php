<?php
namespace Mazi\MailtrapDriver;

use Illuminate\Mail\MailManager;
use Illuminate\Support\ServiceProvider;

class MailtrapServiceProvider extends ServiceProvider
{
    protected $config;

    public function regitser()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/config/mailtrap.php',
            'mail'
        );
    }
    public function boot()
    {
        $this->registerMailtrapDriver();
        $this->config = config('mailtrap');
        $this->publishes([
            __DIR__ . '/config/mailtrap.php' => config_path('mailtrap.php'),
        ], 'config');
    }

    public function registerMailtrapDriver()
    {
        $this->app->resolving(MailManager::class, function (MailManager $manager) {
            $manager->extend('mailtrap', function () {
                $apiToken = $this->config['api_token'];
                $category = $this->config['category'];
                return new MailtrapTransport(
                    $apiToken, $category
                );
            });
        });
    }
}
