<?php

namespace Sysvale\Mongodb;

use Illuminate\Support\ServiceProvider;
use Laravel\Passport\ClientRepository as PassportClientRepository;
use Sysvale\Mongodb\Passport\AuthCode;
use Sysvale\Mongodb\Console\ClientCommand;
use Sysvale\Mongodb\Passport\Client;
use Sysvale\Mongodb\Passport\PersonalAccessClient;
use Sysvale\Mongodb\Passport\Token;
use Laravel\Passport\Console\ClientCommand as PassportClientCommand;
use Laravel\Passport\Console\PurgeCommand as PassportPurgeCommand;
use Laravel\Passport\Passport;
use Laravel\Passport\RefreshTokenRepository as PassportRefreshTokenRepository;
use Laravel\Passport\TokenRepository as PassportTokenRepository;
use Sysvale\Mongodb\Console\PurgeCommand;
use Sysvale\Mongodb\Passport\ClientRepository;
use Sysvale\Mongodb\Passport\RefreshToken;
use Sysvale\Mongodb\Passport\RefreshTokenRepository;
use Sysvale\Mongodb\Passport\TokenRepository;
use Sysvale\Mongodb\Passport\Guards\TokenGuard;
use Illuminate\Support\Facades\Auth;
use League\OAuth2\Server\ResourceServer;
use Laravel\Passport\PassportUserProvider;

class MongodbPassportServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function register()
    {
        //Define use models
        Passport::useAuthCodeModel(AuthCode::class);
        Passport::useClientModel(Client::class);
        Passport::usePersonalAccessClientModel(PersonalAccessClient::class);
        Passport::useRefreshTokenModel(RefreshToken::class);
        Passport::useTokenModel(Token::class);


        // Bind Repositories
        $this->app->bind(PassportClientRepository::class, function () {
            return $this->app->make(ClientRepository::class);
        });

        $this->app->bind(PassportRefreshTokenRepository::class, function () {
            return $this->app->make(RefreshTokenRepository::class);
        });

        $this->app->bind(PassportTokenRepository::class, function () {
            return $this->app->make(TokenRepository::class);
        });

        //Extends Commands
        $this->app->extend(PassportClientCommand::class, function () {
            return new ClientCommand();
        });

        $this->app->extend(PassportPurgeCommand::class, function () {
            return new PurgeCommand();
        });

        $this->registerGuard();
    }

    /**
     * Register the token guard.
     *
     * @return void
     */
    protected function registerGuard()
    {
        Auth::resolved(function ($auth) {
            $auth->extend('passport', function ($app, $name, array $config) {
                return tap($this->makeGuard($config), function ($guard) {
                    app()->refresh('request', $guard, 'setRequest');
                });
            });
        });
    }

    /**
     * Make an instance of the token guard.
     *
     * @param  array  $config
     * @return \Laravel\Passport\Guards\TokenGuard
     */
    protected function makeGuard(array $config)
    {
        return new TokenGuard(
            $this->app->make(ResourceServer::class),
            new PassportUserProvider(Auth::createUserProvider($config['provider']), $config['provider']),
            $this->app->make(TokenRepository::class),
            $this->app->make(ClientRepository::class),
            $this->app->make('encrypter'),
            $this->app->make('request')
        );
    }
}
