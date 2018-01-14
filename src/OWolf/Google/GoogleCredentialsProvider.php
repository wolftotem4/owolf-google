<?php

namespace OWolf\Google;

use Illuminate\Support\ServiceProvider;
use League\OAuth2\Client\Provider\Google;
use OWolf\Credentials\AccessTokenCredentials;
use OWolf\Credentials\ApiKeyCredentials;
use OWolf\Laravel\ProviderManager;
use OWolf\Laravel\UserOAuthManager;

class GoogleCredentialsProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->resolving('owolf.provider', function (ProviderManager $manager, $app) {
            $manager->addDriver('google.oauth', function ($name, $config) {
                $provider = new Google(array_get($config, 'oauth', []));
                return new GoogleOAuthHandler($provider, $name, $config);
            });
        });

        $this->app->resolving('owolf.credentials', function ($manager, $app) {
            $manager->addDriver('google.oauth', function ($name, $config) use ($app) {
                $manager = $this->app->make(UserOAuthManager::class);
                $session = $manager->session($name);
                return new AccessTokenCredentials($session->provider(), $session->getAccessToken());
            });

            $manager->addDriver('google.api', function ($name, $config) {
                $key = array_get($config, 'key');
                return new ApiKeyCredentials(new Google(), $key);
            });
        });
    }
}