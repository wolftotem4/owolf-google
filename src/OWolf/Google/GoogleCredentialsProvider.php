<?php

namespace OWolf\Google;

use Illuminate\Support\ServiceProvider;
use OWolf\Credentials\AccessTokenCredentials;
use OWolf\Credentials\ApiKeyCredentials;
use OWolf\Laravel\UserOAuthManager;
use OWolf\Laravel\Util;

class GoogleCredentialsProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->resolving('owolf.provider', function ($manager, $app) {
            $manager->addDriver('google.oauth', function ($name, $config) {
                $oauth = array_get($config, 'oauth', []);

                $oauth['redirectUri'] = Util::redirectUri(array_get($oauth, 'redirectUri'), $name);

                $provider = new Google($oauth);
                return new GoogleOAuthHandler($provider, $name, $config);
            });
        });

        $this->app->resolving('owolf.credentials', function ($manager, $app) {
            $manager->addDriver('google.oauth', function ($name, $config) use ($app) {
                $manager = $this->app->make(UserOAuthManager::class);
                $session = $manager->session($name)->refreshExpired();
                return new AccessTokenCredentials($session->provider(), $session->getAccessToken());
            });

            $manager->addDriver('google.api', function ($name, $config) {
                $key = array_get($config, 'key');
                return new ApiKeyCredentials(new Google(), $key);
            });
        });
    }
}